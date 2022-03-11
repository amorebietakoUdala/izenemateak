<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Registration;
use App\Form\RegistrationSearchFormType;
use App\Form\RegistrationType;
use App\Repository\CourseRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private $mailer = null;
    private $translator = null;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale}/active/courses", name="app_active_courses")
     */ 
    public function listActiveCourses(CourseRepository $repo) {
        $activeCourses = $repo->findByOpenAndActiveCourses();
        return $this->render('register/listActiveCourses.html.twig', [
            'activeCourses' => $activeCourses,
        ]);
    }

    /**
     * @Route("/{_locale}/register", name="app_register_new")
     */
    public function new(Request $request, EntityManagerInterface $em, CourseRepository $courseRepo, RouterInterface $router): Response
    {
        $registration = new Registration();
        if ( $request->getSession()->get('giltzaUser') === null ) {
            return $this->redirectToRoute('app_giltza');
        }
        if ( $request->getMethod() === Request::METHOD_GET ) {
            $giltzaUser = $request->getSession()->get('giltzaUser');
            $registration->fillWithGiltzaUser($giltzaUser);
        }
        $course = null;
        if ( null !== $request->get('course') ) {
            $course = $courseRepo->find($request->get('course'));
        }
        $registration->setCourse($course);
        $activeCourses = $courseRepo->findByOpenAndActiveCourses();

        $form = $this->createForm(RegistrationType::class, $registration,[
            'locale' => $request->getLocale(),
            'disabled' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var Registration $data */
            if (!$this->checkForErrors($data, $em)) {
                $em->persist($data);
                $em->flush();
                $html = $this->renderView('register/registrationEmail.html.twig', [
                    'registration' => $data,
                ]);
                $subject = $this->translator->trans('mail.subject', [], 'messages', $request->getLocale());
                $this->sendEmail($data->getEmail(), $subject, $html, $this->getParameter('mailerSendBcc'));
                $this->addFlash('success', 'registration.saved');
                if (null === $course) {
                    return $this->redirectToRoute('app_register_new');
                } else {
                    return $this->redirectToRoute('app_register_new', [
                        'course' => $course->getId(),
                    ]);
                }
            }
        }

        return $this->render('register/new.html.twig', [
            'form' => $form->createView(),
            'activeCourses' => $activeCourses,
            'readonly' => false,
            'returnUrl' => $router->generate('app_active_courses'),
        ]);
    }

    /**
     * @Route("{_locale}/admin/registration", name="app_registration_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, RegistrationRepository $repo): Response
    {
        $form = $this->createForm(RegistrationSearchFormType::class, null, [
            'locale' => $request->getLocale(),
        ]);
        $registrations = $repo->findByActiveCourses();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $criteria = $this->removeBlankFilters($data);
            $registrations = $repo->findRegistrationsBy($criteria);
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'registrations' => $registrations,
        ]);
    }

    /**
     * @Route("{_locale}/admin/registration/{id}", name="app_registration_show")
     * @isGranted("ROLE_ADMIN")
     */
    public function show(Request $request, Registration $registration): Response
    {
        $form = $this->createForm(RegistrationType::class, $registration, [
            'disabled' => true,
        ]);
        $returnUrl = $this->getReturnURL($request);

        return $this->renderForm('register/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => true,
            'disabled' => true,
            'registration' => $registration,
            'returnUrl' => $returnUrl,

        ]);
    }

    /**
     * @Route("{_locale}/admin/registration/{id}/edit", name="app_registration_edit")
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Registration $registration, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(RegistrationType::class, $registration, [
            'disabled' => false,
        ]);
        $returnUrl = $this->getReturnURL($request);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Registration $data */
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'registration.saved');
            return $this->redirectToRoute('app_registration_edit',[
                'id' => $registration->getId(),
            ]);
        }

        return $this->renderForm('register/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => false,
            'registration' => $registration,
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * @Route("{_locale}/admin/registration/{id}/delete", name="app_registration_delete", methods={"POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EntityManagerInterface $em, Registration $registration): Response
    {
        if ($this->isCsrfTokenValid('delete'.$registration->getId(), $request->get('_token'))) {
            $em->remove($registration);
            $em->flush();
            $this->addFlash('success','registration_deleted');
            return $this->redirectToRoute('app_registration_index');
        } else {
            $this->addFlash('error','error.invalidCsrfToken');
            return $this->redirectToRoute('app_registration_edit', [
                'id' => $registration->getId(),
            ]);
        }            
    }

    private function sendEmail($to, $subject, $html, bool $sendToHHRR)
    {
        $email = (new Email())
            ->from($this->getParameter('mailerFrom'))
            ->to($to)
            ->subject($subject)
            ->html($html);
        $addresses = [$this->getParameter('mailerFrom')];
        if ($sendToHHRR) {
            $addresses[] = $this->getParameter('mailerBCC');
        }
        foreach ($addresses as $address) {
            $email->addBcc($address);
        }
        $this->mailer->send($email);
    }

    private function checkForErrors(Registration $registration, EntityManagerInterface $em) {
        $repo = $em->getRepository(Registration::class);
        $course = $registration->getCourse();
        $name = $registration->getName();
        $surname1 = $registration->getSurname1();
        $surname2 = $registration->getSurname2();
        $dateOfBirth = $registration->getDateOfBirth();
        $now = new \DateTime();
        $today = new \DateTime($now->format('Y-m-d'));
        
        if( !$course->canRegister($today) ) {
            $this->addFlash('error', 'error.canNotRegisterToday');
            return true;
        }

        $registration = $repo->findOneByDniCourse($registration->getDni(),$course);
        if ( null !== $registration ) {
            $this->addFlash('error', 'error.alreadyRegistered');
            return true;
        }
        $registration = $repo->findOneByNameSurnamesCourse($name, $surname1, $surname2, $course);
        if ( null !== $registration ) {
            $this->addFlash('error', 'error.alreadyRegistered');
            return true;
        }

        $registration = $repo->findOneByNameSurname1DateOfBirthCourse($name,$surname1,$dateOfBirth,$course);
        if ( null !== $registration ) {
            $this->addFlash('error', 'error.alreadyRegistered');
            return true;
        }

        if ( $course->getLimitPlaces() && ( count($course->getRegistrations()) >= $course->getPlaces()) ) {
            $this->addFlash('error', 'error.maxRegistrationsReached');
            return true;
        }

        return false;
    }

    private function removeBlankFilters($filter) {
        $criteria = [];
        foreach ( $filter as $key => $value ) {
            if (null !== $value) {
                $criteria[$key] = $value;
            }
        }
        return $criteria;
    }

    private function getActualUrl(Request $request) {
        return $request->getScheme().'://'.$request->getHost().':'.$request->getPort().$request->getRequestUri();
    }

    private function getReturnURL($request) {
        $referer = $request->headers->get('referer');
        $session = $request->getSession();
        if ($this->getActualUrl($request) !== $referer) {
            $session->set('returnUrl', $referer);
            $returnUrl = $referer; 
        } else {
            $returnUrl = $session->get('returnUrl');
        }
        return $returnUrl;
    }
}

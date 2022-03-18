<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Registration;
use App\Form\CourseSearhFormType;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mime\Email;

class CourseController extends AbstractController
{

    private $mailer = null;
    private $translator = null;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/admin/course/new", name="app_course_new")
     * @isGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CourseType::class, new Course(), [
            'readonly' => false,
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkIfErrorInscriptionDates($form)){
                /** @var Course $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','course.saved');
                return $this->redirectToRoute('app_course_index');
            } 
        }

        return $this->renderForm('course/edit.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
        ]);

    }

    /**
     * @Route("{_locale}/admin/course", name="app_course_index")
     * @isGranted("ROLE_ADMIN")
     */
    public function index(Request $request, CourseRepository $repo): Response
    {
        $ajax = $request->get('ajax') !== null ? $request->get('ajax') : "false";
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        $form = $this->createForm(CourseSearhFormType::class, null, [
            'locale' => $request->getLocale(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $criteria = $this->removeBlankFilters($data);
            $courses = $repo->findCoursesBy($criteria);
            return $this->renderForm("course/$template", [
                'courses' => $courses,
                'form' => $form
            ]);
            }

        $courses = $repo->findBy(['active' => true]);
        return $this->renderForm("course/$template", [
            'courses' => $courses,
            'form' => $form
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/edit", name="app_course_edit")
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EntityManagerInterface $em, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course, [
            'readonly' => false,
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkErrors($form)){
                /** @var Course $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','course.saved');
            } 
        }

        return $this->renderForm('course/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => false,
            'course' => $course,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/delete", name="app_course_delete", methods={"POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EntityManagerInterface $em, Course $course): Response
    {
        if ( count($course->getRegistrations()) > 0 ) {
            $this->addFlash('error','error.course_has_registrations');
            return $this->redirectToRoute('app_course_index');
        } 
        elseif ($this->isCsrfTokenValid('delete'.$course->getId(), $request->get('_token'))) {
            $em->remove($course);
            $em->flush();
            $this->addFlash('success','course_deleted');
            return $this->redirectToRoute('app_course_index');
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }            
    }

    /**
     * @Route("{_locale}/admin/course/{id}/clone", name="app_course_clone")
     * @isGranted("ROLE_ADMIN")
     */
    public function clone(Request $request, EntityManagerInterface $em, Course $course): Response
    {
        $newCourse = $course->clone();
        $form = $this->createForm(CourseType::class, $newCourse, [
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Course $data */
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'course.cloned');
            return $this->redirectToRoute('app_course_index');
        }

        return $this->renderForm('course/clone.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}", name="app_course_show")
     * @isGranted("ROLE_ADMIN")
     */
    public function show(Request $request, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course, [
            'readonly' => true,
            'locale' => $request->getLocale(),
        ]);

        return $this->renderForm('course/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => true,
            'course' => $course,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/details", name="app_course_status_details")
     * @isGranted("ROLE_ADMIN")
     */
    public function raffleDetails(Course $course) {
        $confirmedRegistrations = $course->countConfirmed();
        $rejectedRegistrations = $course->countRejected();
        return $this->renderForm('course/statusDetails.html.twig', [
            'course' => $course,
            'confirmedRegistrations' => $confirmedRegistrations,
            'rejectedRegistrations' => $rejectedRegistrations,

        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/raffle/lottery", name="app_course_raffle_lottery", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function raffleRandomize(Request $request, Course $course, EntityManagerInterface $em) {
        if ($course->getStatus() === Course::STATUS_RAFFLED ) {
            $this->addFlash('error', 'messages.alreadyRaffled');
            return $this->redirectToRoute('app_course_raffle_details', [
                'id' => $course->getId(),
            ]);
        }
        $registrations = $course->getRegistrations();
        $places = $course->getPlaces();
        if (count($registrations) <= $places ) {
            foreach ($registrations as $registration) {
                $registration->setFortunate(true);
                $em->persist($registration);
            }
            $course->setStatus(Course::STATUS_RAFFLED);
            $em->persist($course);
            $fortunates = $registrations;
            $unfortunates = [];
            $this->addFlash('success', 'messages.allAdmited');
        } else {
            $fortunates = [];
            $unfortunates = [];
            $random_keys = $this->getRandomKeys($registrations, $places);
            for ($i=0; $i < count($registrations); $i++ ) {
                $token = $this->generateToken();
                if ( in_array($i, $random_keys) ) {
                    $fortunates[] = $registrations[$i];
                    $registrations[$i]->setFortunate(true);
                } else {
                    $unfortunates[] = $registrations[$i];
                    $registrations[$i]->setFortunate(false);
                }
                $registrations[$i]->setToken($token);
                $em->persist($registrations[$i]);
            }
            $course->setStatus(Course::STATUS_RAFFLED);
            $em->persist($course);
            $this->addFlash('success', 'messages.lotterySuccessfull');
        }
        $em->flush();
        
        return $this->redirectToRoute('app_course_raffle_details', [
            'id' => $course->getId(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/mailing", name="app_course_raffle_mailing", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function sendResultsByEmail(Request $request, Course $course, RegistrationRepository $repo, EntityManagerInterface $em) {
        $fortunates = $repo->findBy([
            'course' => $course,
            'fortunate' => true
        ]);
        $unfortunates = $repo->findBy([
            'course' => $course,
            'fortunate' => false
        ]);

        try {
            $this->sendFortunates($request->getLocale(), $fortunates);
            $this->sendUnFortunates($request->getLocale(), $unfortunates);
            $course->setStatus(Course::STATUS_WAITING_CONFIRMATIONS);
            $em->persist($course);
            $em->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        } finally {
            $this->addFlash('success', 'messages.mailingSuccessfull');
        }
        
        return $this->redirectToRoute('app_course_raffle_details', [
            'id' => $course->getId(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/change-status-waiting-list", name="app_course_change_to_waiting-list", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function changeStatusWaitingList(Course $course, EntityManagerInterface $em) {
        $course->setStatus(Course::STATUS_WAITING_LIST);
        $em->persist($course);
        $em->flush();
        $this->addFlash('success', 'messages.statusChangedToWaitingList');
        return $this->redirectToRoute('app_course_raffle_details', [
            'id' => $course->getid(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/process-waiting-list", name="app_course_email_waiting_list", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function emailWaitingList(Request $request, Course $course, RegistrationRepository $repo, EntityManagerInterface $em) {
        $orderedWaitingList = $repo->findNotConfirmedAndNotFortunate($course);
        $places = $course->getPlaces();
        $confirmedRegistrations = $course->countConfirmed();
        $freePlaces = $places - $confirmedRegistrations;
        for ($i = 0; $i < $freePlaces; $i++) {
            $this->sendFillGapsEmail($orderedWaitingList[$i], $request->getLocale());
        }
        $this->addFlash('success', 'messages.waitingListEmailed');
        return $this->redirectToRoute('app_course_raffle_details', [
            'id' => $course->getid(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/close", name="app_course_close", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function close(Request $request, Course $course, EntityManagerInterface $em) {
        if ($course->getStatus() === Course::STATUS_CLOSED ) {
            $this->addFlash('error', 'messages.alreadyClosed');
            return $this->redirectToRoute('app_course_index');
        }

        $course->setStatus(Course::STATUS_CLOSED);
        $em->persist($course);
        $em->flush();
        $this->addFlash('success', 'course.closed');
        return $this->redirectToRoute('app_course_index');
    }

    private function checkErrors($form): bool {
        /** @var Course $data */
        $data = $form->getData();
        if ( $data->getStartDate() > $data->getEndDate() ) {
            $this->addFlash('error','error.courseStartDateGreaterThanEndDate');
            return true;
        }

        if ( null === $data->getPlaces() && $data->getLimitPlaces() ) {
            $this->addFlash('error','error.placeLimitNotSpecified');
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

    private function sendUnfortunates ($locale, $unfortunates) {
        $subject = $this->translator->trans('mail.unfortunate.subject', [], 'mail', $locale);

        /** @var Registration[] $fortunates */
        foreach ($unfortunates as $unfortunate) {
            $html = $this->renderView('mailing/unfortunateEmail.html.twig', [
                'registration' => $unfortunate,
            ]);
            $this->sendEmail($unfortunate->getEmail(), $subject, $html, false);
        } 
    }

    private function sendFortunates ($locale, $fortunates) {
        $subject = $this->translator->trans('mail.fortunate.subject', [], 'mail', $locale);

        /** @var Registration[] $fortunates */
        foreach ($fortunates as $fortunate) {
            $html = $this->renderView('mailing/fortunateEmail.html.twig', [
                'registration' => $fortunate,
            ]);
            $this->sendEmail($fortunate->getEmail(), $subject, $html, false);
        } 
    }

    private function sendFillGapsEmail(Registration $registration, $locale) {
        $subject = $this->translator->trans('mail.fillGaps.subject', [], 'mail', $locale);
        $html = $this->renderView('mailing/fillGapsEmail.html.twig', [
            'registration' => $registration,
        ]);
        $this->sendEmail($registration->getEmail(), $subject, $html, false);
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

    private function getRandomKeys($registrations, $places) {
        $new_random_keys = [];
        $random_keys = array_rand($registrations->toArray(),$places);
        if (gettype($random_keys) === "integer" || gettype($random_keys) === "string") {
            $new_random_keys [] = $random_keys;
            $random_keys = $new_random_keys;
        } else {
            $new_random_keys = $random_keys;
        }

        return $new_random_keys;
    }

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}

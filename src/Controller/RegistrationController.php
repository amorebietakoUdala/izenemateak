<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Registration;
use App\Form\ActiveActivitysSearchFormType;
use App\Form\RegistrationSearchFormType;
use App\Form\RegistrationType;
use App\Repository\ActivityRepository;
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
     * @Route("/", name="app_home")
     */
    public function home() {
        return $this->redirectToRoute('app_active_activitys');
    }

    /**
     * @Route("/{_locale}/active/activitys", name="app_active_activitys")
     */ 
    public function listActiveActivitys(Request $request, ActivityRepository $repo) {
        $form = $this->createForm(ActiveActivitysSearchFormType::class, null,[
            'locale' => $request->getLocale(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array $data */
            $data = $form->getData();
            $activeActivitys = $repo->findByOpenAndActiveActivitysClasification($data['clasification']);
        } else {
            $activeActivitys = $repo->findByOpenAndActiveActivitys();
        }

        return $this->renderForm('register/listActiveActivitys.html.twig', [
            'activeActivitys' => $activeActivitys,
            'form' => $form,

        ]);
    }

    /**
     * @Route("/{_locale}/register", name="app_register_new")
     */
    public function new(Request $request, EntityManagerInterface $em, ActivityRepository $activityRepo, RouterInterface $router): Response
    {
        $registration = new Registration();
        if ( $request->getSession()->get('giltzaUser') === null ) {
            $request->getSession()->set('returnUrl',$this->getActualUrl($request)); 
            return $this->redirectToRoute('app_giltza');
        }
        if ( $request->getMethod() === Request::METHOD_GET ) {
            $giltzaUser = $request->getSession()->get('giltzaUser');
            $registration->fillWithGiltzaUser($giltzaUser);
        }
        $activity = null;
        if ( null !== $request->get('activity') ) {
            $activity = $activityRepo->find($request->get('activity'));
        }
        $registration->setActivity($activity);
        $activeActivitys = $activityRepo->findByOpenAndActiveActivitys();

        $form = $this->createForm(RegistrationType::class, $registration,[
            'locale' => $request->getLocale(),
            'disabled' => false,
            'admin' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var Registration $data */
            if (!$this->checkForErrors($data, $em)) {
                $em->persist($data);
                $em->flush();
                $html = $this->renderView('mailing/registrationEmail.html.twig', [
                    'registration' => $data,
                ]);
                $subject = $this->translator->trans('mail.subject', [], 'mail', $request->getLocale());
                $this->sendEmail($data->getEmail(), $subject, $html, $this->getParameter('mailerSendBcc'));
                $this->addFlash('success', 'registration.saved');
                if (null === $activity) {
                    return $this->redirectToRoute('app_register_new');
                } else {
                    return $this->redirectToRoute('app_register_new', [
                        'activity' => $activity->getId(),
                    ]);
                }
            }
        }

        return $this->render('register/new.html.twig', [
            'form' => $form->createView(),
            'activeActivitys' => $activeActivitys,
            'readonly' => false,
            'admin' => false,
            'returnUrl' => $router->generate('app_active_activitys'),
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
        $registrations = $repo->findByActiveActivitys();
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
     * @Route("{_locale}/admin/registration/{id}/edit", name="app_registration_edit")
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Registration $registration, Request $request, EntityManagerInterface $em): Response
    {
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        $form = $this->createForm(RegistrationType::class, $registration, [
            'disabled' => false,
            'admin' => true,
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
            'admin' => $admin,
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

    /**
     * @Route("{_locale}/admin/registration/{id}/confirm", name="app_registration_confirm", methods={"GET"})
     */
    public function confirm(Request $request, EntityManagerInterface $em, Registration $registration): Response
    {
        $token = $request->get('token');
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        if ($registration->getToken() !== $token) {
            $this->addFlash('error', 'error.invalidConfirmationToken');
            return $this->render('register/confirmation.html.twig', [
                'error' => true,
                'admin' => $admin,
                'registration' => $registration,
            ]);
        }
        if ( $registration->getConfirmed() === false ) {
            $this->addFlash('error', 'error.alreadyRejected');
            return $this->render('register/confirmation.html.twig', [
                'error' => true,
                'admin' => $admin,
                'registration' => $registration,
            ]);
        }
        if ($registration->getActivity()->isFull()) {
            $this->addFlash('error', 'error.ActivityIsFull');
            return $this->render('register/confirmation.html.twig', [
                'error' => true,
                'admin' => $admin,
                'registration' => $registration,

            ]);
        }
        if ($registration->getConfirmed() ) {
            $this->addFlash('success', 'messages.alreadyConfirmed');
            return $this->render('register/confirmation.html.twig', [
                'error' => false,
                'registration' => $registration,
                'admin' => $admin,
            ]);
        }
        $registration->setConfirmed(true);
        $registration->setConfirmationDate(new \DateTime());
        $em->persist($registration);
        $em->flush();
        $this->sendConfirmationEmail($request->getLocale(), $registration);
        $this->addFlash('success', 'messages.successfullyConfirmed');
        return $this->render('register/confirmation.html.twig', [
            'error' => false,
            'registration' => $registration,
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("{_locale}/admin/registration/{id}/reject", name="app_registration_reject", methods={"GET"})
     */
    public function reject(Request $request, EntityManagerInterface $em, Registration $registration, RegistrationRepository $repo): Response
    {
        $token = $request->get('token');
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        if ($registration->getToken() !== $token) {
            $this->addFlash('error', 'error.invalidRejectToken');
            return $this->render('register/confirmation.html.twig', [
                'error' => true,
                'admin' => $admin,
                'registration' => $registration,
            ]);
        }
        if ( $registration->getConfirmed() !== null && $registration->getConfirmed() === true ) {
            $this->addFlash('error', 'error.alreadyConfirmed');
            return $this->render('register/confirmation.html.twig', [
                'error' => true,
                'admin' => $admin,
                'registration' => $registration,
            ]);
        }
        if ( $registration->getConfirmed() !== null && !$registration->getConfirmed() ) {
            $this->addFlash('success', 'messages.alreadyRejected');
            return $this->render('register/confirmation.html.twig', [
                'error' => false,
                'admin' => $admin,
                'registration' => $registration,
            ]);
        }
        $registration->setConfirmed(false);
        $registration->setConfirmationDate(new \DateTime());
        $em->persist($registration);
        $em->flush();
        if ($registration->getActivity()->getStatus() === Activity::STATUS_WAITING_CONFIRMATIONS) {
            $next = $repo->findNextOnWaitingListActivity($registration->getActivity());
            $this->sendFortunateEmail($request->getLocale(), $next);
        }
        $this->addFlash('success', 'messages.successfullyRejected');
        return $this->render('register/confirmation.html.twig', [
            'error' => false,
            'registration' => $registration,
            'admin' => $admin,
        ]);
    }
/*
    /**
     * @Route("{_locale}/admin/registration/{id}/mailing", name="app_registration_send_confirm_message")
     * @isGranted("ROLE_ADMIN")
     */

    // public function mailing (Request $request, Registration $registration, RegistrationRepository $repo): Response {
    //     $orderedWaitingList = $repo->findNotConfirmedAndNotFortunate($registration->getActivity());
        
    //     foreach ( $orderedWaitingList as $registration) {
    //         $this->send
    //     }

    //     $this->addFlash('success','messages.successfullyMailed');
    //     return $this->redirectToRoute('app_activity_status_details');
    // }

    /**
     * @Route("{_locale}/admin/registration/{id}", name="app_registration_show")
     * @isGranted("ROLE_ADMIN")
     */
    public function show(Request $request, Registration $registration): Response
    {
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        $form = $this->createForm(RegistrationType::class, $registration, [
            'disabled' => true,
            'admin' => true,
        ]);
        $returnUrl = $this->getReturnURL($request);

        return $this->renderForm('register/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => true,
            'disabled' => true,
            'registration' => $registration,
            'returnUrl' => $returnUrl,
            'admin' => $admin,

        ]);
    }

    private function sendConfirmationEmail ($locale, $registration) {
        $subject = $this->translator->trans('mail.confirmationEmail.subject', [], 'mail', $locale);
        $html = $this->renderView('mailing/confirmationEmail.html.twig', [
            'registration' => $registration,
            'cancelation' => false,
        ]);
        $this->sendEmail($registration->getEmail(), $subject, $html, false);
    }

    private function sendFortunateEmail ($locale, $fortunate) {
        $subject = $this->translator->trans('mail.fortunate.subject', [], 'mail', $locale);
        $html = $this->renderView('mailing/fortunateEmail.html.twig', [
            'registration' => $fortunate,
            'cancelation' => true,
        ]);
        $this->sendEmail($fortunate->getEmail(), $subject, $html, false);
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
        $activity = $registration->getActivity();
        $name = $registration->getName();
        $surname1 = $registration->getSurname1();
        $surname2 = $registration->getSurname2();
        $dateOfBirth = $registration->getDateOfBirth();
        $now = new \DateTime();
        $today = new \DateTime($now->format('Y-m-d'));
        
        if( !$activity->canRegister($today) ) {
            $this->addFlash('error', 'error.canNotRegisterToday');
            return true;
        }

        $registration = $repo->findOneByDniActivity($registration->getDni(),$activity);
        if ( null !== $registration ) {
            $this->addFlash('error', 'error.alreadyRegistered');
            return true;
        }
        $registration = $repo->findOneByNameSurnamesActivity($name, $surname1, $surname2, $activity);
        if ( null !== $registration ) {
            $this->addFlash('error', 'error.alreadyRegistered');
            return true;
        }

        $registration = $repo->findOneByNameSurname1DateOfBirthActivity($name,$surname1,$dateOfBirth,$activity);
        if ( null !== $registration ) {
            $this->addFlash('error', 'error.alreadyRegistered');
            return true;
        }

        if ( $activity->getLimitPlaces() && ( count($activity->getRegistrations()) >= $activity->getPlaces()) ) {
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

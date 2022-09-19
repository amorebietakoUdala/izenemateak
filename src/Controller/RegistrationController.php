<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Registration;
use App\Entity\RegistrationExtraField;
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
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegistrationController extends AbstractController
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private Security $security;
    private Pdf $pdf;
    private HttpClientInterface $client;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, Security $security, Pdf $pdf, HttpClientInterface $client)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->security = $security;
        $this->pdf = $pdf;
        $this->client = $client;
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
        if ( $request->getSession()->get('giltzaUser') === null && !$this->security->isGranted('ROLE_USER', $this->getUser()) ) {
            $request->getSession()->set('returnUrl',$this->getActualUrl($request)); 
            return $this->redirectToRoute('app_giltza');
        }
        if ( null !== $request->getSession()->get('giltzaUser') && $request->getMethod() === Request::METHOD_GET ) {
            $giltzaUser = $request->getSession()->get('giltzaUser');
            $registration->fillWithGiltzaUser($giltzaUser);
        }

        $activity = null;
        if ( null !== $request->get('activity') ) {
            $activity = $activityRepo->find($request->get('activity'));
            $registration = $this->createExtraFields($registration, $activity);
        }
        $registration->setActivity($activity);
        $activeActivitys = $activityRepo->findByOpenAndActiveActivitys();

        $form = $this->createForm(RegistrationType::class, $registration,[
            'locale' => $request->getLocale(),
            'disabled' => false,
            'admin' => false,
            'roleUser' => $this->security->isGranted('ROLE_USER', $this->getUser()),
            'new' => true,
            'confirm' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Registration $data */
            $data = $form->getData();
            if (!$this->checkForErrors($data, $em)) {
                $data->setUser($this->getUser());
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->sendRegistrationEmail($data, $request->getLocale());
                $this->addFlash('success', 'registration.saved');
                if (null !== $activity) {
                    return $this->redirectToRoute('app_register_new', [
                        'activity' => $activity->getId(),
                    ]);
                }
                return $this->redirectToRoute('app_register_new');
            }
        }

        return $this->render('register/new.html.twig', [
            'form' => $form->createView(),
            'activeActivitys' => $activeActivitys,
            'readonly' => false,
            'admin' => false,
            'returnUrl' => $router->generate('app_active_activitys'),
            'new' => true,
            'confirm' => false,
        ]);
    }

    private function createExtraFields(Registration $registration, Activity $activity) {
        $extraFields = $activity->getExtraFields();
        foreach ($extraFields as $extraField) {
            $ref = new RegistrationExtraField();
            $ref->setExtraField($extraField);
            $registration->addRegistrationExtraField($ref);
        }
        return $registration;
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
        $new = false;
        $form = $this->createForm(RegistrationType::class, $registration, [
            'disabled' => false,
            'admin' => true,
            'new' => $new,
        ]);
        $returnUrl = $this->getReturnURL($request);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Registration $data */
            $data = $form->getData();
            $registrationExtraFields = $data->getRegistrationExtraFields();
            /** @var RegistrationExtraField ref */
            foreach ($registrationExtraFields as $ref) {
                $ref->setRegistration($data);
            }
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'registration.saved');
            return $this->redirectToRoute('app_registration_edit',[
                'id' => $registration->getId(),
            ]);
        }

        return $this->renderForm('register/edit.html.twig', [
            'form' => $form,
            'new' => $new,
            'readonly' => false,
            'registration' => $registration,
            'returnUrl' => $returnUrl,
            'admin' => $admin,
            'confirm' => false,
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
     * @Route("{_locale}/admin/registration/{id}/pdf", name="app_registration_pdf", methods={"GET", "POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function pdf(Request $request, Registration $registration): Response
    {
        
        $html =  $this->renderView('register/registration-pdf.html.twig',[
            'registration' => $registration,
        ]);
        $pdf = $this->pdf->getOutputFromHtml($html);
        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'registration.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    /**
     * @Route("{_locale}/registration/{id}/confirm", name="app_registration_confirm", methods={"GET", "POST"})
     */
    public function confirm(Request $request, EntityManagerInterface $em, Registration $registration): Response
    {
        $form = $this->createForm(RegistrationType::class, $registration,[
            'locale' => $request->getLocale(),
            'disabled' => false,
            'admin' => false,
            'roleUser' => $this->security->isGranted('ROLE_USER', $this->getUser()),
            'new' => false,
            'confirm' => true,
        ]);
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        $token = $request->get('token');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Registration $data */
            $registration = $form->getData();
            $registration->setUser($this->getUser());
            $error = $this->checkConfirmationRequest($token, $registration);
            if ( $error ) {
                return $this->renderConfirmation(true, $registration, $admin, $form);        
            }
            if ( $registration->getConfirmed() ) {
                $this->addFlash('success', 'messages.alreadyConfirmed');
                return $this->renderConfirmation(false, $registration, $admin, $form);
            }
            if ($this->getParameter('createReceiptsOnConfirmation') && !$registration->getActivity()->isFree() && !$registration->getActivity()->isDomiciled()) {
                $response = $this->createReceipt($registration);
                $responseArray = json_decode($response,true);
                if ( null !== $responseArray && $responseArray['status'] === 'NOK' ) {
                    $this->addFlash('error', 'messages.errorNotConfirmed');
                    return $this->renderConfirmation(true, $registration, $admin, $form);
                }
                $registration->setPaymentURL($responseArray['data']['paymentURL']);
            }
            $registration->setConfirmed(true);
            $registration->setConfirmationDate(new \DateTime());
            $em->persist($registration);
            $em->flush();
            $this->sendConfirmationEmail($registration, $request->getLocale());
            $this->addFlash('success', 'messages.successfullyConfirmed');
            return $this->renderConfirmation(false, $registration, $admin, $form);
        }
        return $this->renderConfirmation(false, $registration, $admin, $form);
    } 

    private function renderConfirmation($error, $registration, $admin, $form = null) {
        return $this->renderForm('register/confirmation.html.twig', [
            'error' => $error,
            'registration' => $registration,
            'admin' => $admin,
            'new' => false,
            'form' => $form !== null ? $form : null,
        ]);
    }

    /**
     * Check if the confirmation request is Ok. Returns true when and error is detected.
     * 
     * @return bool
     */
    private function checkConfirmationRequest($token, Registration $registration) {
        $activity = $registration->getActivity();
        if ( null === $registration->getToken() || $registration->getToken() !== $token) {
            $this->addFlash('error', 'error.invalidConfirmationToken');
            return true;
        }
        if ( $registration->getConfirmed() === false ) {
            $this->addFlash('error', 'error.alreadyRejected');
            return true;
        }
        if ($activity->isFull()) {
            $this->addFlash('error', 'error.activityIsFull');
            return true;
        }
        if ( !$activity->canConfirm() ) {
            $this->addFlash('error', 'error.incorrectStatus');
            return true;
        }
        if ( $activity->isDomiciled() && empty($registration->getPaymentIBANAccount()) ) {
            $this->addFlash('error', 'error.paymentIBANAccountMandatory');
            return true;
        }

        return false;
    }

    /**
     * @Route("{_locale}/registration/{id}/reject", name="app_registration_reject", methods={"GET"})
     */
    public function reject(Request $request, EntityManagerInterface $em, Registration $registration, RegistrationRepository $repo): Response
    {
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        $token = $request->get('token');
        $error = $this->checkRejectionRequest($token, $registration);
        if ( $error ) {
            return $this->renderConfirmation(true, $registration, $admin);
        }
        if ( $registration->getConfirmed() !== null && !$registration->getConfirmed() ) {
            $this->addFlash('success', 'messages.alreadyRejected');
            return $this->renderConfirmation(false, $registration, $admin);
        }
        $registration->setConfirmed(false);
        $registration->setConfirmationDate(new \DateTime());
        $em->persist($registration);
        $em->flush();
        if ($registration->getActivity()->getStatus() === Activity::STATUS_WAITING_CONFIRMATIONS) {
            $next = $repo->findNextOnWaitingListActivity($registration->getActivity());
            $this->sendFortunateEmail($next, $request->getLocale());
            $next->setCalledOnWaitingList(true);
            $em->persist($next);
            $em->flush();
        }
        $this->addFlash('success', 'messages.successfullyRejected');
        return $this->renderConfirmation(false, $registration, $admin);
    }

    /**
     * Check if the rejection request is Ok. Returns true when and error is detected.
     * 
     * @return bool
     */
    private function checkRejectionRequest($token, Registration $registration) {
        if ($registration->getToken() !== $token) {
            $this->addFlash('error', 'error.invalidRejectToken');
            return true;
        }
        if ( $registration->getConfirmed() !== null && $registration->getConfirmed() === true ) {
            $this->addFlash('error', 'error.alreadyConfirmed');
            return true;
        }
        return false;
    }

    /**
     * @Route("{_locale}/admin/registration/{id}", name="app_registration_show")
     * @isGranted("ROLE_ADMIN")
     */
    public function show(Request $request, Registration $registration): Response
    {
        $admin = $request->get('admin') !== null ? $request->get('admin') : false;
        $new = false;
        $form = $this->createForm(RegistrationType::class, $registration, [
            'disabled' => true,
            'admin' => true,
            'new' => $new,

        ]);
        $returnUrl = $this->getReturnURL($request);

        return $this->renderForm('register/edit.html.twig', [
            'form' => $form,
            'new' => $new,
            'readonly' => true,
            'disabled' => true,
            'registration' => $registration,
            'returnUrl' => $returnUrl,
            'admin' => $admin,

        ]);
    }

    private function sendRegistrationEmail(Registration $registration, $locale) {
        if ($registration->getEmail() === null ) {
            return;
        }
        $html = $this->renderView('mailing/registrationEmail.html.twig', [
            'registration' => $registration,
        ]);
        $subject = $this->translator->trans('mail.subject', [], 'mail', $locale);
        $this->sendEmail($registration->getEmail(), $subject, $html, $this->getParameter('mailerSendBcc'));
    }

    private function sendConfirmationEmail (Registration $registration, $locale) {
        if ($registration->getEmail() === null ) {
            return;
        }
        $subject = $this->translator->trans('mail.confirmationEmail.subject', [], 'mail', $locale);
        $html = $this->renderView('mailing/confirmationEmail.html.twig', [
            'registration' => $registration,
            'cancelation' => false,
        ]);
        $this->sendEmail($registration->getEmail(), $subject, $html, false);
    }

    private function sendFortunateEmail (Registration $fortunate, $locale) {
        if ($fortunate->getEmail() === null ) {
            return;
        }
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

    private function createReceipt(Registration $registration) {
        // MOCK Response
        // sleep(7);
        // $response = '{ "status": "OK", "message": "Received", "data": { "recibo": { "numero_recibo": 1298377, "numero_referencia_externa": "froga1", "importe": 5.0, "importe_total": 5.0, "fecha_limite_pago_banco": "2022-06-14T00:00:00+02:00", "tipo_ingreso": { "codigo": "TEXAM", "descripcion": "Tasas de examen", "concepto_c60_au": "025", "concepto_c60": "025" } }, "paymentURL": "https://garapenak.amorebieta-etxano.eus/erreziboak/es/pay/1298377/45624643A" } }';
        // return $response;
        $json = $this->registrationToApiJsonFormat($registration);
        $base64UserPassword = base64_encode($this->getParameter('receiptApiUser').':'.$this->getParameter('receiptApiPassword'));
        $response = $this->client->request('POST',$this->getParameter('receiptApiUrl'),[
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Basic %s', $base64UserPassword),
            ],
            'body' => $json
        ]);
        return $response->getContent();
    }

    private function registrationToApiJsonFormat(Registration $registration) {
        if ( $registration->getSubscriber() && 
            ( $registration->getActivity()->getCostForSubscribers() !== null || $registration->getActivity()->getCostForSubscribers() !== 0 ) 
           ) {
            $price = $registration->getActivity()->getCostForSubscribers();
        } else {
            $price = $registration->getActivity()->getCost();
        }
        $inscription = [
            'inscription' => [
                'nombre' => $registration->getPaymentName(),
                'apellido1' => $registration->getPaymentSurname1(),
                'apellido2' => $registration->getPaymentSurname1(),
                'dni' => $registration->getPaymentDni(),
                'email' => $registration->getEmail(),
                'telefono' => $registration->getTelephone1(),
            ],
            'concept' => $registration->getActivity()->getAccountingConcept(),
            'price' => $price,
            'externalReference' => 'IZEN_'.$registration->getId(),
        ];
        $json = json_encode($inscription);
        return $json;
    }
}

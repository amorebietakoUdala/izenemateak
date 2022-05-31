<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Registration;
use App\Form\ActivityFormType;
use App\Form\ActivitySearhFormType;
use App\Repository\ActivityRepository;
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

class ActivityController extends AbstractController
{

    private $mailer = null;
    private $translator = null;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/admin/activity/new", name="app_activity_new")
     * @isGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ActivityFormType::class, new Activity(), [
            'readonly' => false,
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkErrors($form)){
                /** @var Activity $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','activity.saved');
                return $this->redirectToRoute('app_activity_index');
            } 
        }

        return $this->renderForm('activity/edit.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
        ]);

    }

    /**
     * @Route("{_locale}/admin/activity", name="app_activity_index")
     * @isGranted("ROLE_ADMIN")
     */
    public function index(Request $request, ActivityRepository $repo): Response
    {
        $ajax = $request->get('ajax') !== null ? $request->get('ajax') : "false";
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        $form = $this->createForm(ActivitySearhFormType::class, null, [
            'locale' => $request->getLocale(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $criteria = $this->removeBlankFilters($data);
            $activitys = $repo->findActivitysBy($criteria);
            return $this->renderForm("activity/$template", [
                'activitys' => $activitys,
                'form' => $form
            ]);
            }

        $activitys = $repo->findBy(['active' => true]);
        return $this->renderForm("activity/$template", [
            'activitys' => $activitys,
            'form' => $form
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/edit", name="app_activity_edit")
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EntityManagerInterface $em, Activity $activity): Response
    {
        $form = $this->createForm(ActivityFormType::class, $activity, [
            'readonly' => false,
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkErrors($form)){
                /** @var Activity $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','activity.saved');
            } 
        }

        return $this->renderForm('activity/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => false,
            'activity' => $activity,
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/delete", name="app_activity_delete", methods={"POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EntityManagerInterface $em, Activity $activity): Response
    {
        if ( count($activity->getRegistrations()) > 0 ) {
            $this->addFlash('error','error.activity_has_registrations');
            return $this->redirectToRoute('app_activity_index');
        } 
        elseif ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->get('_token'))) {
            $em->remove($activity);
            $em->flush();
            $this->addFlash('success','activity_deleted');
            return $this->redirectToRoute('app_activity_index');
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }            
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/clone", name="app_activity_clone")
     * @isGranted("ROLE_ADMIN")
     */
    public function clone(Request $request, EntityManagerInterface $em, Activity $activity): Response
    {
        $newActivity = $activity->clone();
        $form = $this->createForm(ActivityFormType::class, $newActivity, [
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $data */
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'activity.cloned');
            return $this->redirectToRoute('app_activity_index');
        }

        return $this->renderForm('activity/clone.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}", name="app_activity_show")
     * @isGranted("ROLE_ADMIN")
     */
    public function show(Request $request, Activity $activity): Response
    {
        $form = $this->createForm(ActivityFormType::class, $activity, [
            'readonly' => true,
            'locale' => $request->getLocale(),
        ]);

        return $this->renderForm('activity/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => true,
            'activity' => $activity,
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/details", name="app_activity_status_details")
     * @isGranted("ROLE_ADMIN")
     */
    public function raffleDetails(Activity $activity) {
        $confirmedRegistrations = $activity->countConfirmed();
        $rejectedRegistrations = $activity->countRejected();
        return $this->renderForm('activity/statusDetails.html.twig', [
            'activity' => $activity,
            'confirmedRegistrations' => $confirmedRegistrations,
            'rejectedRegistrations' => $rejectedRegistrations,

        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/raffle/lottery", name="app_activity_raffle_lottery", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function raffleRandomize(Request $request, Activity $activity, EntityManagerInterface $em) {
        if ($activity->getStatus() === Activity::STATUS_RAFFLED ) {
            $this->addFlash('error', 'messages.alreadyRaffled');
            return $this->redirectToRoute('app_activity_status_details', [
                'id' => $activity->getId(),
            ]);
        }
        $registrations = $activity->getRegistrations();
        $places = $activity->getPlaces();
        if (count($registrations) <= $places ) {
            foreach ($registrations as $registration) {
                $registration->setFortunate(true);
                $em->persist($registration);
            }
            $activity->setStatus(Activity::STATUS_RAFFLED);
            $em->persist($activity);
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
            $activity->setStatus(Activity::STATUS_RAFFLED);
            $em->persist($activity);
            $this->addFlash('success', 'messages.lotterySuccessfull');
        }
        $em->flush();
        
        return $this->redirectToRoute('app_activity_status_details', [
            'id' => $activity->getId(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/mailing", name="app_activity_raffle_mailing", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function sendResultsByEmail(Request $request, Activity $activity, RegistrationRepository $repo, EntityManagerInterface $em) {
        $fortunates = $repo->findBy([
            'activity' => $activity,
            'fortunate' => true
        ]);
        $unfortunates = $repo->findBy([
            'activity' => $activity,
            'fortunate' => false
        ]);

        try {
            $this->sendFortunates($request->getLocale(), $fortunates);
            $this->sendUnFortunates($request->getLocale(), $unfortunates);
            $activity->setStatus(Activity::STATUS_WAITING_CONFIRMATIONS);
            $em->persist($activity);
            $em->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        } finally {
            $this->addFlash('success', 'messages.mailingSuccessfull');
        }
        
        return $this->redirectToRoute('app_activity_status_details', [
            'id' => $activity->getId(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/change-status-waiting-list", name="app_activity_change_to_waiting-list", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function changeStatusWaitingList(Activity $activity, EntityManagerInterface $em) {
        $activity->setStatus(Activity::STATUS_WAITING_LIST);
        $em->persist($activity);
        $em->flush();
        $this->addFlash('success', 'messages.statusChangedToWaitingList');
        return $this->redirectToRoute('app_activity_status_details', [
            'id' => $activity->getid(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/process-waiting-list", name="app_activity_email_waiting_list", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function emailWaitingList(Request $request, Activity $activity, RegistrationRepository $repo, EntityManagerInterface $em) {
        $orderedWaitingList = $repo->findNotConfirmedAndNotFortunate($activity);
        $places = $activity->getPlaces();
        $confirmedRegistrations = $activity->countConfirmed();
        $freePlaces = $places - $confirmedRegistrations;
        for ($i = 0; $i < $freePlaces; $i++) {
            $this->sendFillGapsEmail($orderedWaitingList[$i], $request->getLocale());
        }
        $this->addFlash('success', 'messages.waitingListEmailed');
        return $this->redirectToRoute('app_activity_status_details', [
            'id' => $activity->getid(),
        ]);
    }

    /**
     * @Route("{_locale}/admin/activity/{id}/close", name="app_activity_close", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function close(Request $request, Activity $activity, EntityManagerInterface $em) {
        if ($activity->getStatus() === Activity::STATUS_CLOSED ) {
            $this->addFlash('error', 'messages.alreadyClosed');
            return $this->redirectToRoute('app_activity_index');
        }

        $activity->setStatus(Activity::STATUS_CLOSED);
        $em->persist($activity);
        $em->flush();
        $this->addFlash('success', 'activity.closed');
        return $this->redirectToRoute('app_activity_index');
    }

    private function checkErrors($form): bool {
        /** @var Activity $data */
        $data = $form->getData();
        if ( $data->getStartDate() > $data->getEndDate() ) {
            $this->addFlash('error','error.activityStartDateGreaterThanEndDate');
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

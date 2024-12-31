<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Registration;
use App\Form\ActivityFormType;
use App\Form\ActivitySearchFormType;
use App\Repository\ActivityRepository;
use App\Repository\ExtraFieldRepository;
use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ActivityController extends AbstractController
{

    public function __construct(
        private readonly MailerInterface $mailer, 
        private readonly TranslatorInterface $translator, 
        private readonly HttpClientInterface $client, 
        private readonly ExtraFieldRepository $extraFieldRepo, 
        private readonly EntityManagerInterface $em)
    {
    }

    #[Route(path: '{_locale}/admin/activity/new', name: 'app_activity_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // $concepts = $this->getConcepts();
        $form = $this->createForm(ActivityFormType::class, new Activity(), [
            'readonly' => false,
            'locale' => $request->getLocale(),
            // 'concepts' => $concepts['data'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkErrors($form)){
                /** @var Activity $data */
                $data = $form->getData();
                /** If name is the same takes it from database instead of creating another one */
                $this->checkExtraFields($data->getExtraFields());
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','activity.saved');
                return $this->redirectToRoute('app_activity_index');
            } 
        }

        return $this->render('activity/edit.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
            'accountingConceptServiceUrl' => $this->getParameter('accountingConceptServiceUrl'),
            'username' => $this->getParameter('receiptApiUser'),
            'password' => $this->getParameter('receiptApiPassword'),
            'copyRegistrations' => false,
        ]);
    }

    #[Route(path: '{_locale}/admin/activity', name: 'app_activity_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, ActivityRepository $repo): Response
    {
        $ajax = $request->get('ajax') ?? "false";
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        $form = $this->createForm(ActivitySearchFormType::class, null, [
            'locale' => $request->getLocale(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $criteria = $this->removeBlankFilters($data);
            $activitys = $repo->findActivitysBy($criteria);
            return $this->render("activity/$template", [
                'activitys' => $activitys,
                'form' => $form,
            ]);
            }

        $activitys = $repo->findActivitysBy([
            'active' => true,
        ]);
        return $this->render("activity/$template", [
            'activitys' => $activitys,
            'form' => $form,
            'copyRegistrations' => false,
        ]);
    }

    #[Route(path: '{_locale}/admin/activity/{id}/edit', name: 'app_activity_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, EntityManagerInterface $em, Activity $activity): Response
    {
        //$concepts = $this->getConcepts();
        $form = $this->createForm(ActivityFormType::class, $activity, [
            'readonly' => false,
            'locale' => $request->getLocale(),
        //    'concepts' => $concepts['data'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkErrors($form)){
                /** @var Activity $data */
                $data = $form->getData();
                /** If name is the same takes it from database instead of creating another one */
                $this->checkExtraFields($data->getExtraFields());
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','activity.saved');
            } 
        }

        return $this->render('activity/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => false,
            'activity' => $activity,
            'copyRegistrations' => false,
        ]);
    }

    private function checkExtraFields(Collection $extraFields) {
        foreach($extraFields as $extraField) {
            if ($extraField->getId() === null) {
                $foundExtraField = $this->extraFieldRepo->findOneBy([ 'name' => $extraField->getName() ]);
                if ($foundExtraField !== null) {
                    $extraFields->removeElement($extraField);
                    $extraFields->add($foundExtraField);
                }
            }
        }
        return $extraFields;
    }

    #[Route(path: '{_locale}/admin/activity/{id}/delete', name: 'app_activity_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
            return new Response('messages.invalidCsrfToken', Response::HTTP_UNPROCESSABLE_ENTITY);
        }            
    }

    #[Route(path: '{_locale}/admin/activity/{id}/clone', name: 'app_activity_clone')]
    #[IsGranted('ROLE_ADMIN')]
    public function clone(Request $request, EntityManagerInterface $em, Activity $activity): Response
    {
        $newActivity = $activity->clone();
        //$concepts = $this->getConcepts();
        $form = $this->createForm(ActivityFormType::class, $newActivity, [
            'readonly' => false,
            'locale' => $request->getLocale(),
        //    'concepts' => $concepts['data'],
            'copyRegistrations' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $data */
            $data = $form->getData();
            if ( $data->getUrl() !== null && $data->getCopyRegistrations() ) {
                $this->addFlash('error', 'error.cantCloneRegistrations');
                return $this->render('activity/clone.html.twig', [
                    'form' => $form,
                    'new' => true,
                    'readonly' => false,
                    'copyRegistrations' => true,
                ]);
            }
            if ($data->getCopyRegistrations()) {
                $this->copyRegistrations($data, $activity);
            }
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'activity.cloned');
            return $this->redirectToRoute('app_activity_index');
        }

        return $this->render('activity/clone.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
            'copyRegistrations' => true,
        ]);
    }

    #[Route(path: '{_locale}/admin/activity/{id}', name: 'app_activity_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Request $request, Activity $activity): Response
    {
        //$concepts = $this->getConcepts();
        $form = $this->createForm(ActivityFormType::class, $activity, [
            'readonly' => true,
            'locale' => $request->getLocale(),
        //    'concepts' => $concepts['data'],
        ]);

        return $this->render('activity/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => true,
            'activity' => $activity,
            'copyRegistrations' => false,
        ]);
    }

    #[Route(path: '{_locale}/admin/activity/{id}/details', name: 'app_activity_status_details')]
    #[IsGranted('ROLE_ADMIN')]
    public function raffleDetails(Activity $activity) {
        $confirmedRegistrations = $activity->countConfirmed();
        $rejectedRegistrations = $activity->countRejected();
        return $this->render('activity/statusDetails.html.twig', [
            'activity' => $activity,
            'confirmedRegistrations' => $confirmedRegistrations,
            'rejectedRegistrations' => $rejectedRegistrations,

        ]);
    }

    #[Route(path: '{_locale}/admin/activity/{id}/raffle/lottery', name: 'app_activity_raffle_lottery', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function raffleRandomize(Activity $activity) {
        if ($activity->getStatus() === Activity::STATUS_RAFFLED ) {
            $this->addFlash('error', 'messages.alreadyRaffled');
            return $this->redirectToRoute('app_activity_status_details', [
                'id' => $activity->getId(),
            ]);
        }
        $registrations = $activity->getRegistrations();
        $places = $activity->getPlaces();
        if ( count($registrations) <= $places ) {
            foreach ($registrations as $registration) {
                $registration->setFortunate(true);
                $token = $this->generateToken();
                $registration->setToken($token);
                $this->em->persist($registration);
            }
            $activity->setStatus(Activity::STATUS_RAFFLED);
            $this->em->persist($activity);
            $fortunates = $registrations;
            $unfortunates = [];
            $this->addFlash('success', 'messages.allAdmited');
        } else {
            $fortunates = $unfortunates = [];
            $classifiedRegistrations = $this->getRaffleableAndUnRaffleable($registrations);
            $unRaffleablePlaces = count($classifiedRegistrations['unRaffleable']);
            $fortunates = $classifiedRegistrations['unRaffleable'];
            $this->setUnRaffleableAsFortunate($classifiedRegistrations['unRaffleable']);
            $remainingPlaces = $places - $unRaffleablePlaces;
            $this->raffle($classifiedRegistrations['raffleable'], $remainingPlaces, $fortunates, $unfortunates);
            $activity->setStatus(Activity::STATUS_RAFFLED);
            $this->em->persist($activity);
            $this->addFlash('success', 'messages.lotterySuccessfull');
        }
        $this->em->flush();
        $this->randomizeWaitingList($unfortunates);    
        return $this->redirectToRoute('app_activity_status_details', [
            'id' => $activity->getId(),
        ]);
    }

    /**
     * Sets random waiting list order for unfortunate registrations
     * 
     * @param Registration[]
     */
    private function randomizeWaitingList(array $unfortunates) {
        shuffle($unfortunates);
        $i=1;
        foreach ($unfortunates as $unfortunate) {
            $unfortunate->setWaitingListOrder($i);
            $i++;
            $this->em->persist($unfortunate);
        }
        $this->em->flush();
    }

    #[Route(path: '{_locale}/admin/activity/{id}/mailing', name: 'app_activity_raffle_mailing', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route(path: '{_locale}/admin/activity/{id}/change-status-waiting-list', name: 'app_activity_change_to_waiting-list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeStatusWaitingList(Activity $activity, EntityManagerInterface $em) {
        $activity->setStatus(Activity::STATUS_WAITING_LIST);
        $em->persist($activity);
        $em->flush();
        $this->addFlash('success', 'messages.statusChangedToWaitingList');
        return $this->redirectToRoute('app_activity_status_details', [
            'id' => $activity->getid(),
        ]);
    }

    #[Route(path: '{_locale}/admin/activity/{id}/process-waiting-list', name: 'app_activity_email_waiting_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route(path: '{_locale}/admin/activity/{id}/close', name: 'app_activity_close', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function close(Activity $activity, EntityManagerInterface $em) {
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

    /**
     * @return bool Returns true if an error is found
     */
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

        if ( $data->isDomiciled() && null === $data->getCost() ) {
            $this->addFlash('error','error.costMandatoryIfDomiciled');
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
            if ( null === $unfortunate->getEmail() ) {
                break;
            }
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
            if ( null === $fortunate->getEmail() ) {
                break;
            }
            $html = $this->renderView('mailing/fortunateEmail.html.twig', [
                'registration' => $fortunate,
            ]);
            $this->sendEmail($fortunate->getEmail(), $subject, $html, false);
        } 
    }

    private function sendFillGapsEmail(Registration $registration, $locale) {
        if ( null === $registration->getEmail() ) {
            return;
        }
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
        $random_keys = array_rand($registrations,$places);
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

    private function getConcepts(): array {
        $base64 = base64_encode((string) ($this->getParameter('receiptApiUser').':'.$this->getParameter('receiptApiPassword')));
        $response = $this->client->request('GET', $this->getParameter('accountingConceptServiceUrl'),[
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Basic %s', $base64),
            ],
        ]);

        $json = $response->getContent(true);
        if ( "" !== $json ) {
            $concepts = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $concepts = ['data' => []];
        }
        return $concepts;
    }

    /**
     * Copies the registrations from an activity to another activity.
     * It's used to create an activity that is a continuation from another and the registrations
     * need to be copied from one activity to another. Those registrations can NOT be raffled.
     * 
     * @param Activity $data - The new activity to be filled
     * @param Activity $activity - The activity to take the registrations from
     * @return Activity
     */
    private function copyRegistrations(Activity $data, Activity $activity): Activity {
        $registrations = $activity->getRegistrations();
        foreach ( $registrations as $registration) {
            $newRegistration = new Registration();
            $newRegistration->setUser($this->getUser());
            $data->addRegistration($newRegistration->copyBaseData($registration));
        }
        return $data;
    }

    /**
     * Takes a registrations collection and returns the raffleable and unraffleable registrations in an
     * asociative array. The copied registrations are unraffleable.
     * 
     * @param Collection $registration - Registrations to be classified
     * @return array
     */
    private function getRaffleableAndUnRaffleable (Collection $registrations): array {
        $raffleable = [];
        $unRaffleable = [];
        foreach ($registrations as $registration)
        if ( !$registration->isCopied() ) {
            $raffleable[] = $registration;
        } else {
            $unRaffleable[] = $registration;
        }
        return [
            'raffleable' => $raffleable, 
            'unRaffleable' => $unRaffleable,
        ];
    }

    /**
     * Set unraffleable registrations as fortunate and generates it's tokens for confirmation
     * 
     * @param array $unRaffleableRegistrations - Unraffleable registrations array
     * @return array
     */
    private function setUnRaffleableAsFortunate(array $unRaffleableRegistrations): array {
        foreach ($unRaffleableRegistrations as $unRaffleableRegistration) {
            $token = $this->generateToken();
            $unRaffleableRegistration->setFortunate(true);
            $unRaffleableRegistration->setToken($token);
            $this->em->persist($unRaffleableRegistration);
        }
        return $unRaffleableRegistrations;
    }

    /**
     * Raffles raffleable registrations to fill the remaining places. It takes by reference fortunate an unfortunate arrays to 
     * finish filling them.
     * 
     * @param array $unRaffleableRegistrations - Unraffleable registrations array
     * @return array
     */
    private function raffle($raffleableRegistrations, $remainingPlaces, &$fortunates, &$unfortunates) {
        $random_keys = $this->getRandomKeys($raffleableRegistrations, $remainingPlaces);
        for ($i=0; $i < count($raffleableRegistrations); $i++ ) {
            $token = $this->generateToken();
            if ( in_array($i, $random_keys) ) {
                $fortunates[] = $raffleableRegistrations[$i];
                $raffleableRegistrations[$i]->setFortunate(true);
            } else {
                $unfortunates[] = $raffleableRegistrations[$i];
                $raffleableRegistrations[$i]->setFortunate(false);
            }
            $raffleableRegistrations[$i]->setToken($token);
            $this->em->persist($raffleableRegistrations[$i]);
        }
    }
}

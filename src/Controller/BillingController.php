<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\BillingSearchFormType;
use App\Repository\ActivityRepository;
use App\Service\CsvGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route(path: '{_locale}/billing')]
#[IsGranted('ROLE_IZENEMATEAK')]
class BillingController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepo, 
        private readonly HttpClientInterface $client)
    {
    }

    #[Route(path: '/', name: 'app_billing_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $ajax = $request->get('ajax') ?? "false";
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        $form = $this->createForm(BillingSearchFormType::class, null, [
            'locale' => $request->getLocale(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $criteria = $this->removeBlankFilters($data);
            $activitys = $this->activityRepo->findDomiciledActivitysBy($criteria);
            return $this->render("billing/$template", [
                'activitys' => $activitys,
                'form' => $form
            ]);
        }
        $activitys = $this->activityRepo->findDomiciledActivitysBy([
             'active' => true,
        ]);
        return $this->render("billing/$template", [
            'activitys' => $activitys,
            'form' => $form
        ]);
    }

    #[Route(path: '/{id}/download', name: 'app_billing_download', methods: ['GET'])]
    public function download(Activity $activity, CsvGeneratorService $cgs) {
        $registrations = $activity->getRegistrations();

        if (count($registrations) === 0) {
            $this->addFlash('error', 'error.noRegistrations');
            return $this->redirectToRoute('app_billing_index');
        }
        $response = $this->getConcepts();
        if ($response['status'] !== "OK") {
            $this->addFlash('error', 'error.cantGetConcepts');
            return $this->redirectToRoute('app_billing_index');
        }
        $concepts = $this->adaptConceptsResponse($response);
        $csv = $cgs->createCsv($registrations->toArray(), $concepts);
        return $this->createResponse($csv);
    }

    private function adaptConceptsResponse($response) {
        $concepts = [];
        foreach($response['data'] as $key => $value) {
            $concept = $value['concept'];
            $concepts[$concept['id']] = $value; 
        }
        return $concepts;
    }

    private function createResponse($csv) {
        $response = new Response($csv);
        $response->headers->set('Content-Encoding', 'none');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'receipts.csv'
        );
        
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        return $response;
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

    private function getConcepts() {
        $base64 = base64_encode((string) ($this->getParameter('receiptApiUser').':'.$this->getParameter('receiptApiPassword')));
        $response = $this->client->request('GET', $this->getParameter('accountingConceptServiceUrl'),[
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Basic %s', $base64),
            ],
        ]);
        $json = $response->getContent();
        $concepts = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return $concepts;
    }    
}

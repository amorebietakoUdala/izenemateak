<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ExtraFieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class RestController extends AbstractController
{
    private ExtraFieldRepository $extraFieldRepo;

    public function __construct(ExtraFieldRepository $extraFieldRepo) {
        $this->extraFieldRepo = $extraFieldRepo;
    }

    /**
     * @Route("/activity/{id}/sessions", name="api_activity_sessions", methods={"GET"}, options={"expose" = true})
     */
    public function getSessions(Request $request, Activity $activity, SerializerInterface $serializer): Response
    {
        $sessions = $serializer->serialize($activity->getSessions(), 'json' , [
            'groups' => 'list_sessions',
        ]);

        return new JsonResponse($sessions,200,[],true);
    }

    /**
     * @Route("/extra-fields", name="api_extra_fields", methods={"GET"}, options={"expose" = true})
     */
    public function getExtraFields(Request $request, SerializerInterface $serializer) {
        $locale = $request->get('locale');
        $extraFields = $this->extraFieldRepo->findByNameLike($request->get('name'), $locale);
        return new JsonResponse($serializer->serialize($extraFields,'json',[
            'groups' => 'api'
        ]),200,[],true);
    }

}

<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Repository\ExtraFieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class RestController extends AbstractController
{
    private ExtraFieldRepository $extraFieldRepo;
    private ActivityRepository $activityRepo;

    public function __construct(ExtraFieldRepository $extraFieldRepo, ActivityRepository $activityRepo) {
        $this->extraFieldRepo = $extraFieldRepo;
        $this->activityRepo = $activityRepo;
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

    /**
     * @Route("/activity", name="api_get_activity", methods={"GET"}, options={"expose" = true})
     */
    public function getActivity(Request $request, SerializerInterface $serializer) {
        $id = $request->get('activity');
        $activity = $this->activityRepo->find($id);
        return new JsonResponse($serializer->serialize($activity,'json',[
            'groups' => 'api'
        ]),200,[],true);
    }


}

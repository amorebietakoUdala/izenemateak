<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\ExtraFieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route(path: '/api')]
class RestController extends AbstractController
{
    public function __construct(
        private readonly ExtraFieldRepository $extraFieldRepo, 
        private readonly ActivityRepository $activityRepo)
    {
    }

    #[Route(path: '/extra-fields', name: 'api_extra_fields', methods: ['GET'], options: ['expose' => true])]
    public function getExtraFields(Request $request, SerializerInterface $serializer) {
        $locale = $request->get('locale');
        $extraFields = $this->extraFieldRepo->findByNameLike($request->get('name'), $locale);
        return new JsonResponse($serializer->serialize($extraFields,'json',[
            'groups' => 'api'
        ]),Response::HTTP_OK,[],true);
    }

    #[Route(path: '/activity', name: 'api_get_activity', methods: ['GET'], options: ['expose' => true])]
    public function getActivity(Request $request, SerializerInterface $serializer) {
        $id = $request->get('activity');
        $activity = $this->activityRepo->find($id);
        return new JsonResponse($serializer->serialize($activity,'json',[
            'groups' => 'api'
        ]),Response::HTTP_OK,[],true);
    }


}

<?php

namespace App\Controller;

use App\Entity\Activity;
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

}

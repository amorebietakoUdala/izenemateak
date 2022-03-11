<?php

namespace App\Controller;

use App\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class RestController extends AbstractController
{
    /**
     * @Route("/course/{id}/sessions", name="api_course_sessions", methods={"GET"}, options={"expose" = true})
     */
    public function getSessions(Request $request, Course $course, SerializerInterface $serializer): Response
    {
        $sessions = $serializer->serialize($course->getSessions(), 'json' , [
            'groups' => 'list_sessions',
        ]);

        return new JsonResponse($sessions,200,[],true);
    }

}

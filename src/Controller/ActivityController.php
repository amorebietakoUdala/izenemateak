<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("{_locale}/activity")
 * @isGranted("ROLE_ADMIN")
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/", name="app_activity_index", methods={"GET"})
     */
    public function index(Request $request, ActivityRepository $activityRepository): Response
    {
        $ajax = $request->get('ajax') !== null ? $request->get('ajax') : "false";
        $activities = $activityRepository->findAll();
        $form = $this->createForm(ActivityType::class);
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        return $this->render("activity/$template", [
            'activities' => $activityRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates or updates a Activity
     * 
     * @Route("/new", name="app_activity_save", methods={"GET","POST"})
     */
    public function createOrSave(Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $data */
            $data = $form->getData();
            if (null !== $data->getId()) {
                $activity = $repo->find($data->getId());
                $activity->fill($data);
            }
            $em->persist($activity);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_activity_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('activity/' . $template, [
            'holiday' => $activity,
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}", name="app_activity_show", methods={"GET"})
     */
    public function show(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity, [
            'readonly' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $activity */
            $activity = $form->getData();
            $entityManager->persist($activity);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('activity/' . $template, [
            'activity' => $activity,
            'form' => $form->createView(),
            'readonly' => true,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}/edit", name="app_activity_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Activity $id, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $id, [
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $id */
            $id = $form->getData();
            $entityManager->persist($id);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('activity/' . $template, [
            'activity' => $id,
            'form' => $form->createView(),
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}", name="app_activity_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Activity $id, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $entityManager->remove($id);
            $entityManager->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('app_activity_index');
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }
    }
}

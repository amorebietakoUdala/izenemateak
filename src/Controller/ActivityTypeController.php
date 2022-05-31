<?php

namespace App\Controller;

use App\Entity\ActivityType;
use App\Form\ActivityTypeFormType;
use App\Repository\ActivityTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("{_locale}/activity-type")
 * @isGranted("ROLE_ADMIN")
 */
class ActivityTypeController extends AbstractController
{
    /**
     * @Route("/", name="app_activity_type_index", methods={"GET"})
     */
    public function index(Request $request, ActivityTypeRepository $activityTypeRepository): Response
    {
        $ajax = $request->get('ajax') !== null ? $request->get('ajax') : "false";
        $activityTypes = $activityTypeRepository->findAll();
        $form = $this->createForm(ActivityTypeFormType::class);
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        return $this->render("activityType/$template", [
            'activityTypes' => $activityTypes,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates or updates a Activity
     * 
     * @Route("/new", name="app_activity_type_save", methods={"GET","POST"})
     */
    public function createOrSave(Request $request, ActivityTypeRepository $repo, EntityManagerInterface $em): Response
    {
        $activityType = new ActivityType();
        $form = $this->createForm(ActivityTypeFormType::class, $activityType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ActivityType $data */
            $data = $form->getData();
            if (null !== $data->getId()) {
                $activityType = $repo->find($data->getId());
                $activityType->fill($data);
            }
            $em->persist($activityType);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_activity_type_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('activityType/' . $template, [
            'holiday' => $activityType,
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}", name="app_activity_type_show", methods={"GET"})
     */
    public function show(Request $request, ActivityType $activityType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityTypeFormType::class, $activityType, [
            'readonly' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ActivityType $activityType */
            $activityType = $form->getData();
            $entityManager->persist($activityType);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('activityType/' . $template, [
            'activityType' => $activityType,
            'form' => $form->createView(),
            'readonly' => true,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}/edit", name="app_activity_type_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ActivityType $id, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityTypeFormType::class, $id, [
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
        return $this->render('activityType/' . $template, [
            'activity' => $id,
            'form' => $form->createView(),
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}", name="app_activity_type_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, ActivityType $id, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $entityManager->remove($id);
            $entityManager->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('app_activity_type_index');
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }
    }
}

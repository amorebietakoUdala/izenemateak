<?php

namespace App\Controller;

use App\Entity\Clasification;
use App\Form\ClasificationType;
use App\Repository\ClasificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("{_locale}/clasification")
 * @isGranted("ROLE_ADMIN")
 */
class ClasificationController extends AbstractController
{
    /**
     * @Route("/", name="app_clasification_index", methods={"GET"})
     */
    public function index(Request $request, ClasificationRepository $clasificationRepository): Response
    {
        $ajax = $request->get('ajax') !== null ? $request->get('ajax') : "false";
        $clasifications = $clasificationRepository->findAll();
        $form = $this->createForm(ClasificationType::class);
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        return $this->render("clasification/$template", [
            'clasifications' => $clasificationRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates or updates a Activity
     * 
     * @Route("/new", name="app_clasification_save", methods={"GET","POST"})
     */
    public function createOrSave(Request $request, ClasificationRepository $repo, EntityManagerInterface $em): Response
    {
        $clasification = new Clasification();
        $form = $this->createForm(ClasificationType::class, $clasification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $data */
            $data = $form->getData();
            if (null !== $data->getId()) {
                $clasification = $repo->find($data->getId());
                $clasification->fill($data);
            }
            $em->persist($clasification);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_clasification_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('clasification/' . $template, [
            'holiday' => $clasification,
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}", name="app_clasification_show", methods={"GET"})
     */
    public function show(Request $request, Clasification $clasification, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClasificationType::class, $clasification, [
            'readonly' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $clasification */
            $clasification = $form->getData();
            $entityManager->persist($clasification);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('clasification/' . $template, [
            'clasification' => $clasification,
            'form' => $form->createView(),
            'readonly' => true,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}/edit", name="app_clasification_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Clasification $id, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClasificationType::class, $id, [
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Clasification $id */
            $id = $form->getData();
            $entityManager->persist($id);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('clasification/' . $template, [
            'clasification' => $id,
            'form' => $form->createView(),
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}", name="app_clasification_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Clasification $id, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $entityManager->remove($id);
            $entityManager->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('app_clasification_index');
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }
    }
}

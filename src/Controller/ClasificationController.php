<?php

namespace App\Controller;

use App\Entity\Clasification;
use App\Form\ClasificationType;
use App\Repository\ClasificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '{_locale}/clasification')]
#[IsGranted('ROLE_ADMIN')]
class ClasificationController extends AbstractController
{

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ClasificationRepository $repo,
        private readonly EntityManagerInterface $em,
    )
    {       
    }

    #[Route(path: '/', name: 'app_clasification_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $ajax = $request->get('ajax') ?? "false";
        $form = $this->createForm(ClasificationType::class);
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        return $this->render("clasification/$template", [
            'clasifications' => $this->repo->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * Creates or updates a Activity
     */
    #[Route(path: '/new', name: 'app_clasification_save', methods: ['GET', 'POST'])]
    public function createOrSave(Request $request): Response
    {
        $clasification = new Clasification();
        $form = $this->createForm(ClasificationType::class, $clasification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Clasification $data */
            $data = $form->getData();
            if (null !== $data->getId()) {
                $clasification = $this->repo->find($data->getId());
                $clasification->fill($data);
            }
            $this->em->persist($clasification);
            $this->em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
            return $this->redirectToRoute('app_clasification_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('clasification/' . $template, [
            'holiday' => $clasification,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/{id}', name: 'app_clasification_show', methods: ['GET'])]
    public function show(Request $request, #[MapEntity(id: 'id')] Clasification $clasification): Response
    {
        $form = $this->createForm(ClasificationType::class, $clasification, [
            'readonly' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Activity $clasification */
            $clasification = $form->getData();
            $this->em->persist($clasification);
            $this->em->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('clasification/' . $template, [
            'clasification' => $clasification,
            'form' => $form,
            'readonly' => true,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,));
    }

    #[Route(path: '/{id}/edit', name: 'app_clasification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] Clasification $clasification): Response
    {
        $form = $this->createForm(ClasificationType::class, $clasification, [
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Clasification $id */
            $clasification = $form->getData();
            $this->em->persist($clasification);
            $this->em->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('clasification/' . $template, [
            'clasification' => $clasification,
            'form' => $form,
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,));
    }

    #[Route(path: '/{id}', name: 'app_clasification_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] Clasification $clasification): Response
    {
        if ( !$this->canBeDeleted($clasification) ) {
            return new Response($this->translator->trans('error.clasificationHasActivities'),Response::HTTP_NOT_ACCEPTABLE);
        }
        if ($this->isCsrfTokenValid('delete'.$clasification->getId(), $request->get('_token'))) {
            $this->em->remove($clasification);
            $this->em->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('app_clasification_index');
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function canBeDeleted(Clasification $clasification): bool {
        $activities = $clasification->getActivitys();
        if (count($activities) > 0) {
            return false;
        }
        return true;
    }
}

<?php

namespace App\Controller;

use App\Entity\ActivityType;
use App\Form\ActivityTypeFormType;
use App\Repository\ActivityTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '{_locale}/activity-type')]
#[IsGranted('ROLE_ADMIN')]
class ActivityTypeController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ActivityTypeRepository $activityTypeRepository,
        private readonly EntityManagerInterface $em,

    )
    {       
    }

    #[Route(path: '/', name: 'app_activity_type_index', methods: ['GET'])]
    public function index(Request $request ): Response
    {
        $ajax = $request->get('ajax') ?? "false";
        $activityTypes = $this->activityTypeRepository->findAll();
        $form = $this->createForm(ActivityTypeFormType::class);
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        return $this->render("activityType/$template", [
            'activityTypes' => $activityTypes,
            'form' => $form,
        ]);
    }

    /**
     * Creates or updates a Activity
     */
    #[Route(path: '/new', name: 'app_activity_type_save', methods: ['GET', 'POST'])]
    public function createOrSave(Request $request): Response
    {
        $activityType = new ActivityType();
        $form = $this->createForm(ActivityTypeFormType::class, $activityType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ActivityType $data */
            $data = $form->getData();
            if (null !== $data->getId()) {
                $activityType = $this->activityTypeRepository->find($data->getId());
                $activityType->fill($data);
            }
            $this->em->persist($activityType);
            $this->em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
            return $this->redirectToRoute('app_activity_type_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('activityType/' . $template, [
            'holiday' => $activityType,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/{id}', name: 'app_activity_type_show', methods: ['GET'])]
    public function show(Request $request, #[MapEntity(id: 'id')] ActivityType $activityType): Response
    {
        $form = $this->createForm(ActivityTypeFormType::class, $activityType, [
            'readonly' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ActivityType $activityType */
            $activityType = $form->getData();
            $this->em->persist($activityType);
            $this->em->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('activityType/' . $template, [
            'activityType' => $activityType,
            'form' => $form,
            'readonly' => true,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/{id}/edit', name: 'app_activity_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] ActivityType $activityType): Response
    {
        $form = $this->createForm(ActivityTypeFormType::class, $activityType, [
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ActivityType $activityType */
            $activityType = $form->getData();
            $this->em->persist($activityType);
            $this->em->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('activityType/' . $template, [
            'activity' => $activityType,
            'form' => $form,
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,));
    }

    #[Route(path: '/{id}', name: 'app_activity_type_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] ActivityType $activityType): Response
    {
        if ( !$this->canBeDeleted($activityType) ) {
            return new Response($this->translator->trans('error.activityTypeHasActivities'),Response::HTTP_NOT_ACCEPTABLE);
        }
        if ($this->isCsrfTokenValid('delete'.$activityType->getId(), $request->get('_token'))) {
            $this->em->remove($activityType);
            $this->em->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('app_activity_type_index');
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function canBeDeleted(ActivityType $activityType): bool {
        $activities = $activityType->getActivitys();
        if (count($activities) > 0) {
            return false;
        }
        return true;
    }
}

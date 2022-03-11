<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class CourseController extends AbstractController
{
    /**
     * @Route("{_locale}/admin/course/new", name="app_course_new")
     * @isGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CourseType::class, new Course(), [
            'readonly' => false,
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkIfErrorInscriptionDates($form)){
                /** @var Course $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','course.saved');
                return $this->redirectToRoute('app_course_index');
            } 
        }

        return $this->renderForm('course/edit.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
        ]);

    }

    /**
     * @Route("{_locale}/admin/course", name="app_course_index")
     * @isGranted("ROLE_ADMIN")
     */
    public function index(Request $request, CourseRepository $repo): Response
    {
        $ajax = $request->get('ajax') !== null ? $request->get('ajax') : "false";
        $activities = $repo->findAll();
        $template = $ajax === "true" ? '_list.html.twig' : 'index.html.twig';
        return $this->render("course/$template", [
            'courses' => $activities,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/edit", name="app_course_edit")
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EntityManagerInterface $em, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course, [
            'readonly' => false,
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->checkErrors($form)){
                /** @var Course $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();
                $this->addFlash('success','course.saved');
            } 
        }

        return $this->renderForm('course/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => false,
            'course' => $course,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/delete", name="app_course_delete", methods={"POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, EntityManagerInterface $em, Course $course): Response
    {
        if ( count($course->getRegistrations()) > 0 ) {
            $this->addFlash('error','error.course_has_registrations');
            return $this->redirectToRoute('app_course_index');
        } 
        elseif ($this->isCsrfTokenValid('delete'.$course->getId(), $request->get('_token'))) {
            $em->remove($course);
            $em->flush();
            $this->addFlash('success','course_deleted');
            return $this->redirectToRoute('app_course_index');
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }            
    }

    /**
     * @Route("{_locale}/admin/course/{id}/clone", name="app_course_clone")
     * @isGranted("ROLE_ADMIN")
     */
    public function clone(Request $request, EntityManagerInterface $em, Course $course): Response
    {
        $newCourse = $course->clone();
        $form = $this->createForm(CourseType::class, $newCourse, [
            'locale' => $request->getLocale(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Course $data */
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', 'course.cloned');
            return $this->redirectToRoute('app_course_index');
        }

        return $this->renderForm('course/clone.html.twig', [
            'form' => $form,
            'new' => true,
            'readonly' => false,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}", name="app_course_show")
     * @isGranted("ROLE_ADMIN")
     */
    public function show(Request $request, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course, [
            'readonly' => true,
            'locale' => $request->getLocale(),
        ]);

        return $this->renderForm('course/edit.html.twig', [
            'form' => $form,
            'new' => false,
            'readonly' => true,
            'course' => $course,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/raffle/details", name="app_course_raffle_details")
     * @isGranted("ROLE_ADMIN")
     */
    public function raffleDetails(Course $course) {
        return $this->renderForm('course/raffleDetails.html.twig', [
            'course' => $course,
        ]);
    }

    /**
     * @Route("{_locale}/admin/course/{id}/raffle/randomize", name="app_course_raffle_randomize", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function raffleRandomize(Request $request, Course $course) {
        dd("TODO randomize");
    }

    private function checkErrors($form): bool {
        /** @var Course $data */
        $data = $form->getData();
        if ( $data->getStartDate() > $data->getEndDate() ) {
            $this->addFlash('error','error.courseStartDateGreaterThanEndDate');
            return true;
        }

        if ( null === $data->getPlaces() && $data->getLimitPlaces() ) {
            $this->addFlash('error','error.placeLimitNotSpecified');
            return true;
        }

        return false;
    }
}

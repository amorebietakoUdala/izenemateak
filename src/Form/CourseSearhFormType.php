<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Course;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseSearhFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $builder
        ->add('active',ChoiceType::class,[
            'label' => 'searchForm.active',
            'placeholder' => 'choice.all',
            'choices' => [
                'choice.yes' => true,
                'choice.no' => false,
            ],
            'data' => true,
            'required' => false,
        ])
        ->add('startDate', DateType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
            'attr' => ['class' => 'js-datepicker'],
            'label' => 'searchForm.creationStartDate',
            'required' => false,
        ])
        ->add('endDate', DateType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
            'attr' => ['class' => 'js-datepicker'],
            'label' => 'searchForm.creationEndDate',
            'required' => false,
        ])
        ->add('limitPlaces',ChoiceType::class,[
            'label' => 'searchForm.limitPlaces',
            'choices' => [
                'choice.all' => null,
                'choice.yes' => true,
                'choice.no' => false,
            ],
            'data' => null,
            'required' => false,
        ])
        ->add('activity', EntityType::class, [
            'class' => Activity::class,
            'label' => 'course.activity',
            'placeholder' => '',
            'required' => false,
        ])
        ->add('status', ChoiceType::class,[
            'label' => 'course.status',
            'choices' => [
                'course.status.'.Course::STATUS_PREINSCRIPTION => Course::STATUS_PREINSCRIPTION,
                'course.status.'.Course::STATUS_RAFFLED => Course::STATUS_RAFFLED,
                'course.status.'.Course::STATUS_WAITING_CONFIRMATIONS => Course::STATUS_WAITING_CONFIRMATIONS,
                'course.status.'.Course::STATUS_WAITING_LIST => Course::STATUS_WAITING_LIST,
                'course.status.'.Course::STATUS_CLOSED => Course::STATUS_CLOSED,
            ],
            'required' => false,
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'locale' => 'es',
        ]);
    }
}

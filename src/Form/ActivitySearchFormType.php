<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivitySearchFormType extends AbstractType
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
        ->add('activityType', EntityType::class, [
            'class' => ActivityType::class,
            'label' => 'activity.activityType',
            'placeholder' => '',
            'required' => false,
        ])
        ->add('status', ChoiceType::class,[
            'label' => 'activity.status',
            'choices' => [
                'activity.status.'.Activity::STATUS_PREINSCRIPTION => Activity::STATUS_PREINSCRIPTION,
                'activity.status.'.Activity::STATUS_RAFFLED => Activity::STATUS_RAFFLED,
                'activity.status.'.Activity::STATUS_WAITING_CONFIRMATIONS => Activity::STATUS_WAITING_CONFIRMATIONS,
                'activity.status.'.Activity::STATUS_WAITING_LIST => Activity::STATUS_WAITING_LIST,
                'activity.status.'.Activity::STATUS_CLOSED => Activity::STATUS_CLOSED,
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

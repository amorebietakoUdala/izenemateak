<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Entity\Clasification;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $builder
            ->add('clasification', EntityType::class, [
                'label' => 'activity.clasification',
                'placeholder' => 'choice.all',
                'class' => Clasification::class,
                'choice_label' => function ($clasification) use ($locale) {
                    if ('es' === $locale) {
                        return $clasification->getDescriptionEs();
                    } else {
                        return $clasification->getDescriptionEu();
                    }
                },
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
                'placeholder' => 'activity.status.open',
                'choices' => [
                    'activity.status.'.Activity::STATUS_PREINSCRIPTION => Activity::STATUS_PREINSCRIPTION,
                    'activity.status.'.Activity::STATUS_RAFFLED => Activity::STATUS_RAFFLED,
                    'activity.status.'.Activity::STATUS_WAITING_CONFIRMATIONS => Activity::STATUS_WAITING_CONFIRMATIONS,
                    'activity.status.'.Activity::STATUS_WAITING_LIST => Activity::STATUS_WAITING_LIST,
                    'activity.status.'.Activity::STATUS_CLOSED => Activity::STATUS_CLOSED,
                ],
                'required' => false,
            ])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'locale' => 'es',
        ]);
    }
}

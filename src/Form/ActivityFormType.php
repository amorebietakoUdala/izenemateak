<?php

namespace App\Form;

use App\Entity\ActivityType;
use App\Entity\Clasification;
use App\Entity\Activity;
use App\Entity\ExtraField;
use App\Repository\ClasificationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $locale = $options['locale'];
        $status = $options['data']->getStatus() !== null ? $options['data']->getStatus() : 0;
        $concepts = $options['concepts'] !== null ? $options['concepts'] : [];
        $builder
            ->add('clasification',EntityType::class,[
                'label' => 'activity.clasification',
                'class' => Clasification::class,
                'constraints' => [
                    new NotBlank(),
                ],
                'choice_label' => function ($type) use ($locale) {
                    if ('es' === $locale) {
                        return $type->getDescriptionEs();
                    } else {
                        return $type->getDescriptionEu();
                    }
                },
                'query_builder' => function( ClasificationRepository $repo ) {
                    return $repo->findAllQB();
                },
                'disabled' => $readonly,
            ])
            ->add('activityType',EntityType::class,[
                'label' => 'activity.activityType',
                'class' => ActivityType::class,
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('nameEs',null,[
                'label' => 'activity.nameEs',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('nameEu',null,[
                'label' => 'activity.nameEu',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('turnEs',null,[
                'label' => 'activity.turnEs',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('turnEu',null,[
                'label' => 'activity.turnEu',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'activity.startDate',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'activity.endDate',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('places', IntegerType::class, [
                'label' => 'activity.places',
                'disabled' => $readonly,
                'required' => false,
            ])
            ->add('limitPlaces', CheckboxType::class, [
                'label' => 'activity.limitPlaces',
                'disabled' => $readonly,
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
                'disabled' => $readonly || $status !== Activity::STATUS_PREINSCRIPTION,
            ])
            ->add('cost', NumberType::class, [
                'label' => 'activity.cost',
                'disabled' => $readonly,
                'required' => false,
            ])
            ->add('costForSubscribers', NumberType::class, [
                'label' => 'activity.costForSubscribers',
                'disabled' => $readonly,
                'required' => false,
            ])
            ->add('domiciled', CheckboxType::class, [
                'label' => 'activity.domiciled',
                'disabled' => $readonly,
                'required' => false,
            ])
            ->add('accountingConcept', ChoiceType::class, [
                'label' => 'activity.accountingConcept',
                'disabled' => $readonly,
                'choices' => $this->prepareChoices($concepts, $locale),
                'required' => true,
            ])

            ->add('active', null, [
                'label' => 'activity.active',
                'disabled' => $readonly,
            ])
            ->add('extraFields', CollectionType::class, [
                'label' => 'activity.extraFields',
                'disabled' => $readonly,
                'entry_type' => ExtraFieldType::class,
                'entry_options' => [
                    'attr' => ['class' => 'list-group-item'],
                ],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
        ;
    }

    private function prepareChoices($concepts, $locale) {
        $choices = [];
        foreach ($concepts as $concept) {
            $conceptElem = $concept['concept']; 
            if ($locale === 'es' ) {
                $choices[$conceptElem['name']] = $conceptElem['id'];
            } else {
                $choices[$conceptElem['name_eu']] = $conceptElem['id'];
            }
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
            'readonly'  => false,
            'locale'  => false,
            'concepts' => [],
        ]);
    }
}

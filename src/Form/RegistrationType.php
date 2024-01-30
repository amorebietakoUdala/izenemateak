<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Registration;
use App\Repository\ActivityRepository;
use App\Validator\IsValidDNI;
use App\Validator\IsValidIBAN;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $disabled = $options['disabled'];
        $registration = $options['data'];
        $activity = $registration->getActivity();
        $forMe = $registration->getForMe();
        $admin = $options['admin'];
        $new = $options['new'];
        $confirm = $options['confirm'];
        $roleUser = $options['roleUser'];
        $builder
            ->add('forMe', CheckboxType::class,[
                'label' => $forMe ? 'register.forMe' : 'register.notForMe',
                'required' => false,
                'disabled' => $disabled || $confirm, 
            ])
            ->add('email', null, [
                'label' => 'register.email',
                'required' => false,
                'constraints' => [
                    new Email(),
                ],
                'disabled' => $disabled || $confirm,
            ])
            ->add('dni', null,[
                'label' => 'register.dni',
                'attr' => ['readonly' => $forMe && $roleUser === null? 'readonly' : false],
                'constraints' => [
                    new IsValidDNI(),
                ],
                'disabled' => $disabled || $confirm,
            ])
            ->add('name', null, [
                'label' => 'register.name',
                'attr' => ['readonly' => $forMe && $roleUser === null? 'readonly' : false],
                'disabled' => $disabled || $confirm,
            ])
            ->add('surname1', null, [
                'label' => 'register.surname1',
                'attr' => ['readonly' => $forMe && $roleUser === null? 'readonly' : false],
                'disabled' => $disabled || $confirm,
            ])
            ->add('surname2', null, [
                'label' => 'register.surname2',
                'attr' => ['readonly' => $forMe && $roleUser === null? 'readonly' : false],
                'disabled' => $disabled || $confirm,
            ])
            ->add('dateOfBirth', DateType::class, [
                'label' => 'register.dateOfBirth', 
                'disabled' => $disabled || $confirm,
                'widget' => 'single_text',
                            'html5' => false,
                            'attr' => [
                                'class' => 'js-datepicker', 
                                'placeholder' => 'register.dateOfBirth.placeHolder'
                            ],
            ])
            ->add('school', null,[
                'label' => 'register.school',
                'disabled' => $disabled || $confirm,
            ])
            ->add('telephone1', null,[
                'label' => 'register.telephone1',
                'disabled' => $disabled || $confirm,
            ])
            ->add('telephone2', null,[
                'label' => 'register.telephone2',
                'disabled' => $disabled || $confirm,
            ])
            ->add('subscriber', CheckboxType::class,[
                'label' => 'register.subscriber',
                'required' => false,
                'disabled' => $disabled || $confirm,
            ])
            ->add('representativeDni', null,[
                'label' => 'register.representativeDni',
                'attr' => ['readonly' => !$forMe && $roleUser === null? 'readonly' : false],
                'constraints' => [
                    new IsValidDNI(),
                ],
                'disabled' => $disabled || $confirm,
            ])
            ->add('representativeName', null,[
                'label' => 'register.representativeName',
                'attr' => ['readonly' => !$forMe && $roleUser === null? 'readonly' : false],
                'disabled' => $disabled || $confirm,
            ])
            ->add('representativeSurname1', null,[
                'label' => 'register.representativeSurname1',
                'attr' => ['readonly' => !$forMe && $roleUser === null? 'readonly' : false],
                'disabled' => $disabled || $confirm,
            ])
            ->add('representativeSurname2', null,[
                'label' => 'register.representativeSurname2',
                'attr' => ['readonly' => !$forMe && $roleUser === null? 'readonly' : false],
                'disabled' => $disabled || $confirm,
            ])
            ->add('registrationExtraFields', CollectionType::class,[
                'label' => 'register.extraFields',
                'disabled' => $disabled || $confirm,
                'entry_type' => RegistrationExtraFieldType::class,
                'entry_options' => [
                    'activity' => $activity,
                    'attr' => ['class' => 'list-group-item'],
                ],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ;
            if ($new) {
                $actityFieldOptions = [
                    'class' => Activity::class,
                    'label' => 'register.activity',
                    'placeholder' => 'placeholder.choose',
                    'choice_label' => function ($type) use ($locale) {
                        if ('es' === $locale) {
                            return $type->getNameEs().' '.$type->getTurnEs();
                        } else {
                            return $type->getNameEu().' '.$type->getTurnEu();
                        }
                    },
                    'disabled' => $disabled || $confirm,
                ];
                if ($admin) {
                    array_merge($actityFieldOptions,[
                        'query_builder' => fn(ActivityRepository $repo) => $repo->findAll()
                    ]);
                } else { 
                    array_merge($actityFieldOptions,[
                        'query_builder' => fn(ActivityRepository $repo) => $repo->findByOpenAndActiveActivitysQB()
                    ]);
                }
                $builder
                    ->add('activity', EntityType::class, $actityFieldOptions);
        } else {
                $builder
                ->add('activity', EntityType::class, [
                    'class' => Activity::class,
                    'label' => 'register.activity',
                    'placeholder' => 'placeholder.choose',
                    'choice_label' => function ($type) use ($locale) {
                        if ('es' === $locale) {
                            return $type->getNameEs().' '.$type->getTurnEs();
                        } else {
                            return $type->getNameEu().' '.$type->getTurnEu();
                        }
                    },
                    'disabled' => $disabled || $confirm,
                ]);
                $required = $confirm && !$activity->isFree();
                if ( !$new && ( $confirm || $admin ) ) {
                    $builder->add('paymentWho', ChoiceType::class,[
                        'label' => 'register.payer',
                        'required' => $required,
                        'disabled' => $disabled && !$confirm,
                        'choices' => [
                            'register.payerPrincipal' => 0,
                            'register.payerRepresentative' => 1,
                            'register.payerOther' => 2,
                        ],
                        'multiple' => false,
                        'expanded' => true,
                    ])
                    ->add('paymentDni', null,[
                        'label' => 'register.paymentDni',
                        'constraints' => [
                            new IsValidDNI(),
                        ],
                        'disabled' => $disabled && !$confirm,
                        'required' => $required, 
                    ])
                    ->add('paymentName', null, [
                        'label' => 'register.name',
                        'disabled' => $disabled && !$confirm,
                        'required' => $required,
                    ])
                    ->add('paymentSurname1', null, [
                        'label' => 'register.surname1',
                        'disabled' => $disabled && !$confirm,
                        'required' => $required,
                    ])
                    ->add('paymentSurname2', null, [
                        'label' => 'register.surname2',
                        'disabled' => $disabled && !$confirm,
                        'required' => false,
                    ]);
                    if ($activity !== null && $activity->isDomiciled()) {
                        $builder
                            ->add('paymentIBANAccount', null,[
                                'label' => 'register.paymentIBANAccount',
                                'constraints' => [
                                    new IsValidIBAN(),
                                ],
                                'disabled' => $disabled && !$confirm,
                                'required' => $required,
                            ]);
                    }
                }
                
            }
            if ( !$new && $admin ) {
                $builder
                    ->add('fortunate', CheckboxType::class,[
                        'label' => 'registration.fortunate',
                        'disabled' => true 
                    ])
                    ->add('confirmed', CheckboxType::class,[
                        'label' => 'registration.confirmed',
                        'disabled' => true 
                    ])
                ;
            } 
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
            'locale' => 'es',
            'disabled' => false,
            'admin' => false,
            'roleUser' => false,
            'new' => true,
            'activity' => null,
            'confirm' => false,
        ]);
    }
}

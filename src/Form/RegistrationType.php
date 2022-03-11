<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Registration;
use App\Entity\Session;
use App\Repository\CourseRepository;
use App\Validator\IsValidDNI;
use App\Validator\IsValidIBAN;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
        $forMe = $registration->getForMe();
        $builder
            ->add('forMe', CheckboxType::class,[
                'label' => $forMe ? 'register.forMe' : 'register.notForMe',
                'required' => false,
                'disabled' => $disabled, 
            ])
            ->add('email', null, [
                'label' => 'register.email',
                'constraints' => [
                    new Email(),
                    new NotBlank(),
                ],
                'disabled' => $disabled,
            ])
            ->add('dni', null,[
                'label' => 'register.dni',
                'attr' => ['readonly' => $forMe ? 'readonly' : false],
                'constraints' => [
                    new IsValidDNI(),
                ],
                'disabled' => $disabled,
            ])
            ->add('name', null, [
                'label' => 'register.name',
                'attr' => ['readonly' => $forMe ? 'readonly' : false],
                'disabled' => $disabled,
            ])
            ->add('surname1', null, [
                'label' => 'register.surname1',
                'attr' => ['readonly' => $forMe ? 'readonly' : false],
                'disabled' => $disabled,
            ])
            ->add('surname2', null, [
                'label' => 'register.surname2',
                'attr' => ['readonly' => $forMe ? 'readonly' : false],
                'disabled' => $disabled,
            ])
            ->add('dateOfBirth', DateType::class, [
                'label' => 'register.dateOfBirth', 
                'disabled' => $disabled,
                'widget' => 'single_text',
                            'html5' => false,
                            'attr' => ['class' => 'js-datepicker'],
                            
            ])
            ->add('telephone1', null,[
                'label' => 'register.telephone1',
                'disabled' => $disabled,
            ])
            ->add('telephone2', null,[
                'label' => 'register.telephone2',
                'disabled' => $disabled,
            ])
            ->add('subscriber', CheckboxType::class,[
                'label' => 'register.subscriber',
                'required' => false,
                'disabled' => $disabled,
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'label' => 'register.course',
                'placeholder' => 'placeholder.choose',
                'choice_label' => function ($type) use ($locale) {
                    if ('es' === $locale) {
                        return $type->getNameEs();
                    } else {
                        return $type->getNameEu();
                    }
                },
                'query_builder' => function( CourseRepository $repo ) {
                    return $repo->findByOpenAndActiveCoursesQB();
                },
                'disabled' => $disabled,
            ])
            ->add('representativeDni', null,[
                'label' => 'register.representativeDni',
                'attr' => ['readonly' => !$forMe ? 'readonly' : false],
                'constraints' => [
                    new IsValidDNI(),
                ],
                'disabled' => $disabled,
            ])
            ->add('representativeName', null,[
                'label' => 'register.representativeName',
                'attr' => ['readonly' => !$forMe ? 'readonly' : false],
                'disabled' => $disabled,
            ])
            ->add('representativeSurname1', null,[
                'label' => 'register.representativeSurname1',
                'attr' => ['readonly' => !$forMe ? 'readonly' : false],
                'disabled' => $disabled,
            ])
            ->add('representativeSurname2', null,[
                'label' => 'register.representativeSurname2',
                'attr' => ['readonly' => !$forMe ? 'readonly' : false],
                'disabled' => $disabled,
            ])
            ->add('paymentDni', null,[
                'label' => 'register.paymentDni',
                'constraints' => [
                    new IsValidDNI(),
                ],
                'disabled' => $disabled,
            ])
            ->add('paymentIBANAccount', null,[
                'label' => 'register.paymentIBANAccount',
                'constraints' => [
                    new IsValidIBAN(),
                ],
                'disabled' => $disabled,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
            'locale' => 'es',
            'disabled' => false,
        ]);
    }
}

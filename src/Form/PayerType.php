<?php

namespace App\Form;

use App\Validator\IsValidDNI;
use App\Validator\IsValidIBAN;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $disabled = $options['disabled'];
        $builder
            ->add('paymentWho', ChoiceType::class,[
                'label' => 'register.payer',
                'required' => true,
                'disabled' => $disabled,
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
                'disabled' => $disabled,
            ])
            ->add('paymentName', null, [
                'label' => 'register.name',
                'disabled' => $disabled,
            ])
            ->add('paymentSurname1', null, [
                'label' => 'register.surname1',
                'disabled' => $disabled,
            ])
            ->add('paymentSurname2', null, [
                'label' => 'register.surname2',
                'disabled' => $disabled,
            ])
            ->add('paymentIBANAccount', null,[
                'label' => 'register.paymentIBANAccount',
                'constraints' => [
                    new IsValidIBAN(),
                ],
                'disabled' => $disabled,
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'disabled' => false,
        ]);
    }
}

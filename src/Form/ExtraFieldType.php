<?php

namespace App\Form;

use App\Entity\ExtraField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExtraFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $builder
            ->add('name', null, [
                'label' => 'extraField.name',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly
            ])
            ->add('nameEu', null, [
                'label' => 'extraField.nameEu',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExtraField::class,
            'readonly' => false,
        ]);
    }
}

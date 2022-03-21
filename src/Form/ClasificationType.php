<?php

namespace App\Form;

use App\Entity\Clasification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClasificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $builder
            ->add('id', HiddenType::class,[
                'disabled' => $readonly,
            ])
            ->add('descriptionEs',null,[
                'label' => 'clasification.descriptionEs',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly
            ])
            ->add('descriptionEu',null,[
                'label' => 'clasification.descriptionEu',
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
            'data_class' => Clasification::class,
            'readonly' => false,
        ]);
    }
}

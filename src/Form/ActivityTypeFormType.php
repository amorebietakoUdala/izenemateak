<?php

namespace App\Form;

use App\Entity\ActivityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActivityTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $builder
            ->add('id', HiddenType::class,[
                'disabled' => $readonly,
            ])
            ->add('name',null,[
                'label' => 'activityType.name',
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
            'data_class' => ActivityType::class,
            'readonly' => false,
        ]);
    }
}

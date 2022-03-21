<?php

namespace App\Form;

use App\Entity\Clasification;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiveCourseSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $builder
            ->add('clasification', EntityType::class, [
                'placeholder' => 'choice.all',
                'class' => Clasification::class,
                'choice_label' => function ($type) use ($locale) {
                    if ('es' === $locale) {
                        return $type->getDescriptionEs();
                    } else {
                        return $type->getDescriptionEu();
                    }
                },
                'required' => false,           
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'locale' => 'es'
            // Configure your form options here
        ]);
    }
}

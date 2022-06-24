<?php

namespace App\Form;

use App\Entity\ExtraField;
use App\Entity\RegistrationExtraField;
use App\Repository\ExtraFieldRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationExtraFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $options['activity'];
        $locale = $options['locale'];
        $builder
            ->add('extraField', EntityType::class, [
                'class' => ExtraField::class,
                'label' => 'registrationExtraField.extraField',
                'placeholder' => 'placeholder.choose',
                'choice_label' => function ($extraField) use ($locale) {
                    if ('es' === $locale) {
                        return $extraField->getName();
                    } else {
                        return $extraField->getName();
                    }
                },
                'query_builder' => function( ExtraFieldRepository $repo ) use ($activity) {
                    return $repo->findByActivityQB($activity);
                },
                'disabled' => true,
            ])
            ->add('value', null, [
                'label' => 'registrationExtraField.value',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationExtraField::class,
            'locale' => 'es',
            'activity' => null,
        ]);
    }
}

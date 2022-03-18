<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $builder
            ->add('dni', null, [
                'label' => 'searchForm.dni',
                'required' => false,
            ] )
            ->add('course',EntityType::class,[
                'class' => Course::class,
                'label' => 'searchForm.course',
                'placeholder' => 'placeholder.choose',
                'choice_label' => function ($type) use ($locale) {
                    if ('es' === $locale) {
                        return $type->getNameEs();
                    } else {
                        return $type->getNameEu();
                    }
                },
                'required' => false,
            ])
            ->add('active',ChoiceType::class,[
                'label' => 'searchForm.active',
                'placeholder' => 'choice.all',
                'choices' => [
                    'choice.active' => true,
                    'choice.unActive' => false,
                ],
                'data' => true,
                'required' => false,
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'course.startDate',
                'required' => false,
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'course.endDate',
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

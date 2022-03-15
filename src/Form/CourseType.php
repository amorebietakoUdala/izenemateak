<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Course;
use App\Entity\Status;
use App\Repository\StatusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $locale = $options['locale'];
        $statusNumber = $options['data']->getStatus() !== null ? $options['data']->getStatus()->getStatusNumber() : 0;
        $builder
            ->add('activity',EntityType::class,[
                'label' => 'course.activity',
                'class' => Activity::class,
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('nameEs',null,[
                'label' => 'course.nameEs',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('nameEu',null,[
                'label' => 'course.nameEu',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('turnEs',null,[
                'label' => 'course.turnEu',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('turnEu',null,[
                'label' => 'course.turnEu',
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
                'label' => 'course.startDate',
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
                'label' => 'course.endDate',
                'constraints' => [
                    new NotBlank(),
                ],
                'disabled' => $readonly,
            ])
            ->add('places', IntegerType::class, [
                'label' => 'course.places',
                'disabled' => $readonly,
                'required' => false,
            ])
            ->add('limitPlaces', null, [
                'label' => 'course.limitPlaces',
                'disabled' => $readonly,
                'data' => false,
            ])
            ->add('status',EntityType::class,[
                'label' => 'course.status',
                'class' => Status::class,
                'choice_label' => function ($type) use ($locale) {
                    if ('es' === $locale) {
                        return $type->getDescriptionEs();
                    } else {
                        return $type->getDescriptionEu();
                    }
                },
                'query_builder' => function( StatusRepository $repo ) {
                    return $repo->findAllQB();
                },
                'disabled' => $readonly || $statusNumber !== Status::PREINSCRIPTION,
            ])
            ->add('active', null, [
                'label' => 'course.active',
                'disabled' => $readonly,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
            'readonly'  => false,
            'locale'  => false,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Clasification;
use App\Entity\Course;
use App\Repository\ClasificationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $locale = $options['locale'];
        $status = $options['data']->getStatus() !== null ? $options['data']->getStatus() : 0;
        $builder
            ->add('clasification',EntityType::class,[
                'label' => 'course.clasification',
                'class' => Clasification::class,
                'constraints' => [
                    new NotBlank(),
                ],
                'choice_label' => function ($type) use ($locale) {
                    if ('es' === $locale) {
                        return $type->getDescriptionEs();
                    } else {
                        return $type->getDescriptionEu();
                    }
                },
                'query_builder' => function( ClasificationRepository $repo ) {
                    return $repo->findAllQB();
                },
                'disabled' => $readonly,
            ])
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
                'empty_data' => false,
            ])
            ->add('status', ChoiceType::class,[
                'label' => 'course.status',
                'choices' => [
                    'course.status.'.Course::STATUS_PREINSCRIPTION => Course::STATUS_PREINSCRIPTION,
                    'course.status.'.Course::STATUS_RAFFLED => Course::STATUS_RAFFLED,
                    'course.status.'.Course::STATUS_WAITING_CONFIRMATIONS => Course::STATUS_WAITING_CONFIRMATIONS,
                    'course.status.'.Course::STATUS_WAITING_LIST => Course::STATUS_WAITING_LIST,
                    'course.status.'.Course::STATUS_CLOSED => Course::STATUS_CLOSED,
                ],
                'disabled' => $readonly || $status !== Course::STATUS_PREINSCRIPTION,
            ])
            ->add('cost', NumberType::class, [
                'label' => 'course.cost',
                'disabled' => $readonly,
                'required' => false,
            ])
            ->add('deposit', NumberType::class, [
                'label' => 'course.deposit',
                'disabled' => $readonly,
                'required' => false,
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

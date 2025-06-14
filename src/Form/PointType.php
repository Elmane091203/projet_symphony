<?php

namespace App\Form;

use App\Entity\Point;
use App\Entity\Zone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class PointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom ne peut pas être vide',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('zone', EntityType::class, [
                'class' => Zone::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une zone',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nb_habitants', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nombre d\'habitants ne peut pas être vide',
                    ]),
                    new Positive([
                        'message' => 'Le nombre d\'habitants doit être positif',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nb_symptomatiques', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nombre de cas symptomatiques ne peut pas être vide',
                    ]),
                    new Positive([
                        'message' => 'Le nombre de cas symptomatiques doit être positif',
                    ]),
                    new LessThanOrEqual([
                        'value' => $options['data']->getNbHabitants(),
                        'message' => 'Le nombre de cas symptomatiques ne peut pas dépasser le nombre d\'habitants',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nb_positifs', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nombre de cas positifs ne peut pas être vide',
                    ]),
                    new Positive([
                        'message' => 'Le nombre de cas positifs doit être positif',
                    ]),
                    new LessThanOrEqual([
                        'value' => $options['data']->getNbHabitants(),
                        'message' => 'Le nombre de cas positifs ne peut pas dépasser le nombre d\'habitants',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Point::class,
        ]);
    }
} 
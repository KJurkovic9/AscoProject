<?php

namespace App\Form;

use App\Entity\Calculation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CalculationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roofSurface', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Roof surface is manditaory field'])
                ]
            ])
            ->add('yearlyConsumption', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Yearly consumption is manditaory field'])
                ]
            ])
            ->add('roofPitch', RoofPitchType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Roof pitch is manditaory field']),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Roof surface must be greater than zero'
                    ]),
                    new Positive(['message' => 'Roof surface must be a positive number'])
                ]
            ])
            ->add('roofOrientation', WorldOrientationType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Roof orientation is manditaory field'])
                ]
            ])
            ->add('location', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Location is manditaory field'])
                ]
            ])
            ->add('lat', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Latitude is manditaory field'])
                ]
            ])
            ->add('lng', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Longitude is manditaory field'])
                ]
            ])
            ->add('lifespan', NumberType::class)
            ->add('budget', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calculation::class,
        ]);
    }
}

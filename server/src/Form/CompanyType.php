<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['constraints' => [
                new NotBlank(['message' => 'Company name is manditaory field']),
                new Length(['max' => 255, 'maxMessage' => 'Company name cannot be longer than {{ limit }} characters']),
            ]])
            ->add('email', TextType::class, ['constraints' => [
                new NotBlank(['message' => 'Company emial is manditaory field']),
                new Length(['max' => 255, 'maxMessage' => 'Company email cannot be longer than {{ limit }} characters']),
            ]])
            ->add('lat', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Latitude is manditaory field'])
                ]
            ])
            ->add('lng', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => '>Longitude is manditaory field'])
                ]
            ])
            ->add('radius', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Radius is manditaory field'])
                ]
            ])
            ->add('mobile', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Company mobile is manditaory field']),
                    new Length(['max' => 15, 'maxMessage' => 'Company mobile cannot be longer than {{ limit }} characters']),
                ]
            ])
            ->add('location', TextType::class, [
                    'constraints' => [
                        new NotBlank(['message' => 'Company location is manditaory field']),
                        new Length(['max' => 255, 'maxMessage' => 'Company location cannot be longer than {{ limit }} characters']),
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}

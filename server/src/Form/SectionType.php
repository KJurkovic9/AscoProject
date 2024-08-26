<?php

namespace App\Form;

use App\Entity\Guide;
use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Section title is manditaory field']),
                    new Length(['max' => 255, 'maxMessage' => 'Section title cannot be longer than {{ limit }} characters']),
                ]
            ])
            ->add('text', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Section text is manditaory field']),
                    new Length(['max' => 2048, 'maxMessage' => 'Section text cannot be longer than {{ limit }} characters']),
                ]
            ])
            ->add('image')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}

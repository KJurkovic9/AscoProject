<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextType::class, ['constraints' => [
                new NotBlank(['message' => 'Review text is manditaory field']),
                new Length(['max' => 1024, 'maxMessage' => 'Review text cannot be longer than {{ limit }} characters']),
            ]])
            ->add('rating', NumberType::class, ['constraints' => [
                new NotBlank(['message' => 'Review rating is manditaory field']),
                new Length(['max' => 5.0, 'maxMessage' => 'Review rating cannot be longer than {{ limit }} characters']),
                new GreaterThanOrEqual(['value' => 0.0, 'message' => 'Review rating must be greater than or equal to {{ compared_value }}'])
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}

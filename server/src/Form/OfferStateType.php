<?php

namespace App\Form;

use App\Controller\OfferState;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OfferStateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(OfferState::toArray()),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

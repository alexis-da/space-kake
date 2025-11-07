<?php

namespace App\Form;

use App\Entity\Cakes;
use App\Entity\Clients;
use App\Entity\Reviews;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '1 étoile' => 1,
                    '2 étoiles' => 2,
                    '3 étoiles' => 3,
                    '4 étoiles' => 4,
                    '5 étoiles' => 5,
                ],
                'placeholder' => 'Choisissez une note',
                'required' => true,
                'label' => 'Note (sur 5)',
            ])
            ->add('cake', EntityType::class, [
                'class' => Cakes::class,
                'choice_label' => 'title',
                'label' => 'Gâteau',
                'placeholder' => 'Choisissez un gâteau',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reviews::class,
        ]);
    }
}

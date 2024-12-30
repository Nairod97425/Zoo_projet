<?php
// src/Form/CompteRenduType.php

namespace App\Form;

use App\Entity\CompteRendu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Animal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CompteRenduType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Entrez la description du compte rendu'],
            ])
            ->add('animal', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'name',
                'label' => 'Animal',
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'À faire' => 'à faire',
                    'Fait' => 'fait',
                ],
                'label' => 'Statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompteRendu::class,
        ]);
    }
}
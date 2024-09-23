<?php
// src/Form/AnimalType.php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Habitat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajoute cette ligne
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Prenom', TextType::class)
            ->add('Race', TextType::class)
            ->add('images', FileType::class, [
                'label' => 'Image',
                'required' => false, // Si le champ est facultatif
                // 'multiple' => true, // Permet la sélection de plusieurs fichiers
            ])
            // ->add('Etat', TextType::class, ['required' => false])
            ->add('habitat', EntityType::class, [
                'class' => Habitat::class,
                'choice_label' => 'Name',
                'placeholder' => 'Choisissez un habitat',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter l\'animal',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}

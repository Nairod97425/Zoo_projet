<?php
// src/Form/AnimalType.php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Habitat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('species', TextType::class, [
                'label' => 'Espèce'
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
            ])
            ->add('healthStatus', TextType::class, [
                'label' => 'État de santé'
            ])
            ->add('habitat', EntityType::class, [
                'class' => Habitat::class,
                'choice_label' => 'name',
                'placeholder' => 'Aucun habitat',
                'required' => false,
            ])
            ->add('feedingSchedule', TextareaType::class, [
                'label' => 'Programme alimentaire'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Images',
                'multiple' => true,  // Autoriser plusieurs fichiers
                'mapped' => false,    // Gardez cela si vous gérez les images manuellement
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}

<?php
// src/Form/HabitatType.php

namespace App\Form;

use App\Entity\Habitat;
use App\Entity\Animal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class HabitatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name') // Le champ pour le nom de l'habitat
            ->add('description') // Le champ pour la description
            ->add('images', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true, // Si vous voulez permettre plusieurs fichiers
            ])            
            ->add('animals', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Habitat::class, // Associe ce formulaire à l'entité Habitat
        ]);
    }
}

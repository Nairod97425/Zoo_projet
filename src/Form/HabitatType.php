<?php

namespace App\Form;

use App\Entity\Habitat;
use App\Entity\Animal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajoute cette ligne
use Symfony\Component\OptionsResolver\OptionsResolver;

class HabitatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Description')
            ->add('images', FileType::class, [
                'label' => 'Image',
                'required' => false, // Si le champ est facultatif
                // 'multiple' => true, // Permet la sélection de plusieurs fichiers
            ])
            ->add('animals', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'Prenom',
                'multiple' => true,  // Pour sélectionner plusieurs animaux
                'expanded' => true,  // Pour utiliser des checkboxes
                'by_reference' => false,  // Important pour la gestion des collections
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Habitat::class,
        ]);
    }
}

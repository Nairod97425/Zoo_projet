<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Habitat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avis', TextareaType::class, [
                'label' => 'Votre avis',
                'attr' => ['placeholder' => 'Écrivez votre avis ici...']
            ]);
            // ->add('statut', TextType::class, [
            //     'label' => 'Statut'
            // ])
            // ->add('habitat', EntityType::class, [
            //     'class' => Habitat::class,
            //     'choice_label' => 'name',
            //     'label' => 'Habitat'
            // ])
            // ->add('save', SubmitType::class, [
            //     'label' => 'Envoyer'
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}

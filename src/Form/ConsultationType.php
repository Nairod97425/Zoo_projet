<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\Animal;
use App\Entity\Habitat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('Nombre_de_consultation')
            ->add('Habitat_id', EntityType::class, [
                'class' => Habitat::class,
                'choice_label' => 'Name',
            ])
            ->add('Animal_id', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'Prenom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}

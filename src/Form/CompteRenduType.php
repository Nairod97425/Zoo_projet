<?php
// src/Form/CompteRenduType.php
namespace App\Form;

use App\Entity\CompteRendu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompteRenduType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('animal', EntityType::class, [
                'class' => 'App\Entity\Animal',
                'choice_label' => 'name',  // Assurez-vous que 'name' est une propriété de l'entité Animal
                'label' => 'Animal',
                'required' => true,
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
        $resolver->setDefaults([
            'data_class' => CompteRendu::class,
        ]);
    }
}

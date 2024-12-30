<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Email']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',

                'attr' => ['placeholder' => 'Mot de passe']
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Employer' => 'ROLE_EMPLOYE',
                    'Vétérinaire' => 'ROLE_VETERINAIRE',
                    'Utilisateur' => 'ROLE_USER'
                ],
                'expanded' => true, // Utilisez false pour une liste déroulante ou true pour des boutons radio
                'multiple' => true, // Permet la sélection de plusieurs rôles
                'placeholder' => 'Sélectionnez un ou plusieurs rôles',
                'label' => 'Rôles',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         // Type repeated qui permet de dire à symfony que pour une même propriété nous avons besoin de deux champs ayant le même contenu
         ->add('new_password',RepeatedType::class,[
            'type' => PasswordType::class,
            'invalid_message' => 'Le mot de passe et la confirmation doivent être identique',
            'label' => 'Mot de passe',
            'required' => true,
            'first_options' => [
                'label' => 'Nouveau mot de passe',
                'attr' => ['placeholder' => 'Nouveau mot de passe']
            ],
            'second_options' => [
                'label' => 'Confirmer le nouveau mot de passe',
                'attr' => ['placeholder' => 'Confirmer le nouveau mot de passe']
                ]             
         ])            

        ->add('submit', SubmitType::class,[
            'label' => "Reinitialiser mon mot de passe", 
                'attr' => [
                    'class' => 'btn btn-info text-white col-md-12'
                ]
            
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

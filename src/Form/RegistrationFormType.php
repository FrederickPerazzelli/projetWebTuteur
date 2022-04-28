<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('first_name', TextType::class, [
                'label' => 'Prenom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prenom',
                    ]),
                    new Regex([
                        'pattern' =>'/^([A-Z][a-z]+)(-[A-Z][a-z]+)*$/',
                        'message' => 'Le prenom doit être comme suit: Manuel'
                        ])
                ]
            ])

            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                    new Regex([
                        'pattern' =>'/^([A-Z][a-z]+)(-[A-Z][a-z]+)*$/',
                        'message' => 'Le nom de famille doit être comme suit: Turcotte'
                        ])
                ]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Courriel',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrez votre adresse courriel',
                    ]),
                    new Regex([
                        'pattern' => '/^((([a-z0-9._-]+)@([a-z0-9._-]+)\.([a-z]{2,6}))|(([a-z0-9._-]+)@\[(([\d]){1,3}\.){3}[\d]{1,3}\]))$/',
                        'message' => 'Le email doit être comme suit: bidon@bidon.com'
                        ])
                ]
            ])

            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])

            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre numéro de téléphone',
                    ]),
                    new Regex([
                        'pattern' => '/^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/',
                        'message' => 'Le numéro de téléphone doit être comme suit: 999-999-9999 '
                    ]),
                ]
            ])
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#?$%&*()\/\\\\])/',
                        'message' => 'Votre mot de passe doit contenire une majuscule, une minuscule, un chiffre et un caractère special'
                        ])
                ],
                
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

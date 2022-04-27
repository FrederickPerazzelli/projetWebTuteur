<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Courriel'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('institution', TextType::class, [
                'label' => 'Institution d\'enseignement/d\'étude',
                'required' => false
            ])
            ->add('field', TextType::class, [
                'label' => 'Programme d\'étude',
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/',
                        'message' => 'Veuillez respecter le format requis'
                    ])
                ]
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'format' => 'yyyy MM dd',
            ])  
            ->add('validAccount', CheckboxType::class, [
                'label' => 'Compte actif',
                'required' => false
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Type de compte',
                'expanded' => false,
                'multiple' => false
            ])
            ->add('masteredSubject', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'Catégorie',
                'multiple' => false
            ])
            ->setMethod('POST')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

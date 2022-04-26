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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Courriel',
                'disabled' => 'disabled'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'disabled' => 'disabled'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'disabled' => 'disabled'
            ])
            ->add('institution', TextType::class, [
                'label' => 'Institution d\'enseignement/d\'étude',
                'disabled' => 'disabled',
                'required' => false
            ])
            ->add('field', TextType::class, [
                'label' => 'Programme d\'étude',
                'disabled' => 'disabled',
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'disabled' => 'disabled',
                'required' => false
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'disabled' => 'disabled'
            ])  
            ->add('validAccount', CheckboxType::class, [
                'label' => 'Compte actif',
                'disabled' => 'disabled',
                'required' => false
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Type de compte',
                'expanded' => false,
                'multiple' => false,
                'disabled' => 'disabled'
            ])
            ->add('masteredSubject', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'Catégorie',
                'multiple' => false,
                'disabled' => 'disabled'
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

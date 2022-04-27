<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Category;
use App\Entity\Meeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class MeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', BirthdayType::class, [
                'label' => 'Date'
            ])
            ->add('meetingTime', TimeType::class, [
                'label' => 'Heure'
            ])
            ->add('motive', TextType::class, [
                'label' => 'Sujet'
            ])
            ->add('location', TextType::class, [
                'label' => 'Location'
            ])
            ->add('comments', TextType::class, [
                'label' => 'Commentaire'
            ])
            ->add('student', EmailType::class, [
                'label' => 'Étudiant'
            ])
            ->add('tutor', EmailType::class, [
                'label' => 'Tuteur'
            ])
            ->add('status', TextType::class, [
                'label' => 'Status'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meeting::class,
        ]);
    }
}

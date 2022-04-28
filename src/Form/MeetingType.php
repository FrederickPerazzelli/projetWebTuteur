<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Status;
use App\Entity\Meeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'label' => 'Date',
                'disabled' => True
            ])
            ->add('meetingTime', TimeType::class, [
                'label' => 'Heure',
                'disabled' => True
            ])
            ->add('motive', TextType::class, [
                'label' => 'Sujet',
                'disabled' => True
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'disabled' => True
            ])
            ->add('comments', TextareaType::class, [
                'label' => 'Commentaire',
                'disabled' => True
            ])

            ->add('student', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Étudiant',
                'disabled' => True
            ])

            ->add('tutor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Tuteur',
                'disabled' => True
            ])

            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label' => 'Status',
                'disabled' => True
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

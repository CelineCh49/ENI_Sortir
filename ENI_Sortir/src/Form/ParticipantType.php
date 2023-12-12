<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=>'Email',
                'empty_data' => '',
            ])
            ->add('motPasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'required' => false,  // <-- This makes it optional
                'mapped' => false,
                'first_options'  => ['label' => 'Mot de passe', 'required' => false],  // <-- Also here
                'second_options' => ['label' => 'Confirmer le mot de passe', 'required' => false],  // <-- And here
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au minimum {{ limit }} caractères.',
                        'max' => 4000,
                        'maxMessage' => 'Le mot de passe doit contenir au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom',
                'empty_data' => '',
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prénom',
                'empty_data' => '',
            ])
            ->add('pseudo', TextType::class, [
                'label'=>'Pseudo',
                'empty_data' => '',
            ])
            ->add('telephone', TextType::class, [
                'label'=>'Téléphone',
                'empty_data' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

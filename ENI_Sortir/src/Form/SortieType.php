<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
            ->add('nom',TextType::class,[
                'label'=> 'Nom de la sortie'
            ])
            ->add('dateHeureDebut',DateTimeType::class ,[
                'label'=> 'Date et heure de la sortie',
                'html5'=>true,
                'widget'=>'single_text',
            ])
            ->add('dateLimiteInscription',DateType::class ,[
                'label'=> 'Date limite d\'inscription',
                'html5'=>true,
                'widget'=>'single_text',
            ])
            ->add('nbInscriptionsMax',TextType::class,[
                'label'=> 'Nombre de places'
            ])
            ->add('duree', IntegerType::class ,[
                'label'=> 'Durée',
            ])
            ->add('infosSortie', TextareaType::class,[
                'label'=> 'Description et infos' 
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom', 
                'label' => 'Ville',
                'placeholder' => 'Sélectionnez une ville', 
                'required' => true, 
                'mapped'=> false,// important: permet d'utiliser l'entité ville sans qu'elle ait d'attribut dans l'entité Sortie
            ])
            ->add('lieu', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'required' => true,
                
            ])

           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

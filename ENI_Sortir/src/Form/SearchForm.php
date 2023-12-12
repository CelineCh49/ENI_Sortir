<?php

namespace App\Form;

use App\Controller\SecurityController;
use App\Data\SearchData;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentUserCampus = $this->security->getUser()->getCampus();

        $builder
            ->add('q', TextType::class, [
                'label' =>false,
                'required'=> false,
                'attr'=>[
                    'placeholder'=> 'Rechercher'
                ]
                ])
            ->add('campus', EntityType::class, [
                    'class' => Campus::class,
                    'data' => $currentUserCampus,// permet de mettre des données par défaut
                    'choice_label' => 'nom',
                    'required' => false,
                    'placeholder' => 'Tous les campus',
                    'label' => 'Campus :'
                    ])
            ->add('dateMin', DateType::class,[
                'label' =>false,
                'html5'=>true,
                'widget'=>'single_text',
                'required'=>false,
            ])
            ->add('dateMax', DateType::class,[
                'label' =>false,
                'html5'=>true,
                'widget'=>'single_text',
                'required'=>false,
            ])
            ->add('sortiesOrganisees', CheckboxType::class,[
                'label'=> 'Sorties dont je suis l\'organisateur/trice',
                'required'=>false,
  
                
            ])
            ->add('sortiesInscrit', CheckboxType::class,[
                'label'=> 'Sorties auxquelles je suis inscrit/e',
                'required'=>false
            ])
            ->add('sortiesPasInscrit', CheckboxType::class,[
                'label'=> 'Sorties auxquelles je ne suis pas inscrit/e',
                'required'=>false
            ])
            ->add('sortiesPassee', CheckboxType::class,[
                'label'=> 'Sorties passées',
                'required'=>false
            ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> SearchData::class,
            'method'=>'GET',
            'csrf_protection'=> false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}
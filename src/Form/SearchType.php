<?php

namespace App\Form;

use App\Controller\Classes\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{   
    //On utilise buildForm pour créer un formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string' ,TextType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'votre recherche..',
                    'class' => 'form-control-sm'
                    ]                
                ])
                    // Type qui permet de lié à l'entité category pour qu'elle reflète la class category
            ->add('categorie',EntityType::class,[
                'label' => false,
                'required' => false,
                // La classe avec laquel il faut faire un lien (Permet d'afficher les données de table category)
                'class' => Category::class,
                // Permet de séléctionné plusieur valeur
                'multiple' => true,
                // Permet de voir une vue en checkbox
                'expanded' => true
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn-block btn-info'
                    ]
                ]);
    }

    // Permet de paramétrer les options du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Ici on utilisera les paramétre de la classe Search présente dans App\Controller\Classes\Search            
            'data_class' => Search::class,
            // Méthode du formulaire qui permettra de faire transiter les données par l'URL
            'method' => 'GET',
            //Permet d'éviter les failles de sécurité. La protection CSRF fonctionne en ajoutant un champ masqué à votre formulaire qui contient une valeur que seuls vous et votre utilisateur connaissez.
            'crsf_protection' => false
        ]);
    }

    //Fonction qui permet de récupérer les données dans un tableau 
    public function getBlockPrefix()
    {
        return '';
    }
}   
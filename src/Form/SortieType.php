<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortieType extends AbstractType
{

    /*  Builder du formulaire de création de sorties
     *  L'organisateur ne renseigne ni son pseudo, ni l'état de la sortie (se fait automatiquement)
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :',
                'trim' => true,
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description :',
                'trim' => true,
                'required' => true,
            ])
            /*->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
            ])


                        ->add('ville', EntityType::class, array(
                            'class' => Ville::class,
                            'query_builder' => function (EntityRepository $er)  {
                                return $er -> createQueryBuilder('Ville') -> orderBy('Ville.nom', 'ASC') ;
                            },
                            'choice_label' => 'nom',
                        ))


            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'ville.nom',
                'label' => 'ville'
            ])
*/
            ->add('lieu', EntityType::class, [
        'class' => Lieu::class,
        'choice_label' => 'nom',
    ])
        ->add('datedebut', DateTimeType::class, [
            'label' => 'Date de début de sortie :',
            'required' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ])
        ->add('duree', IntegerType::class, [
            'label' => 'Durée de la sortie (en heures) :',
            'required' => true,
        ])
        ->add('dateLimiteInscription', DateType::class, [
            'label' => 'Date limite d\'inscription :',
            'required' => true,
            'widget' => 'single_text',
        ])
        ->add('nbinscriptionsmax', IntegerType::class, [
            'label' => 'Nombre maximum de participants :',
            'required' => true,
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Publier',
        ]);

            /* TODO:Gérer l'upload/affichage d'une photo
            ->add('urlphoto')
     */

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'trait_choices' => null,
        ]);
    }
}

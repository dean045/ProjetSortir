<?php

namespace App\Form;

use App\Entity\Sortie;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('nom', StringType::class,[
                'label' => 'Nom :',
                'placeholder' => 'Votre nom',
            ])
            ->add('datedebut', DateTimeType::class,[
                'label' => 'Date de début de sortie :',
            ])
            ->add('duree', IntegerType::class,[
                'label' => 'Durée de la sortie :',
                'placeholder' => 'Renseignez la durée en heures'
            ])
            ->add('dateLimiteInscription', \DateTime::class,[
                'label' => 'Date limite d\'inscription :',
            ])
            ->add('nbinscriptionsmax', IntegerType::class,[
                'label' => 'Nombre maximum de participants :'
            ])
            ->add('description', TextType::class,[
                'label' => 'Description :',
                'placeholder' => 'Décrivez tous les détails de votre événement ',
            ])
            ->add('urlphoto');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

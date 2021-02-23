<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortieType extends AbstractType
{

    /*  Builder du formulaire de création de sorties
     *  L'organisateur ne renseigne ni son pseudo, ni l'état de la sortie (se fait automatiquement)
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie :',
                'trim' => true,
                'required' => true,
            ])

            ->add('description', TextareaType::class,[
                'label' => 'Description :',
                'trim' => true,
                'required' => true,
            ])

            ->add('datedebut', DateType::class,[
                'label' => 'Date de début de sortie :',
                'required' => true,
                'widget' => 'single_text',
            ])

            ->add('timedebut', TimeType::class, [
                'label' => 'Heure de début : ',
                'mapped' => false,
                'widget' => 'single_text'
            ])

            ->add('duree', IntegerType::class,[
                'label' => 'Durée de la sortie (en heures) :',
                'required' => true,
            ])

            ->add('dateLimiteInscription', DateType::class,[
                'label' => 'Date limite d\'inscription :',
                'required' => true,
                'widget' => 'single_text',
            ])

            ->add('timelimite', TimeType::class, [
                'label' => 'Heure limite d\'inscription :',
                'mapped' => false,
                'widget' => 'single_text',
            ])

            ->add('nbinscriptionsmax', IntegerType::class,[
                'label' => 'Nombre maximum de participants :',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier une sortie',
            ])

            /* TODO:Gérer l'upload/affichage d'une photo
            ->add('urlphoto')
            */

            ->addEventListener(FormEvents::POST_SET_DATA,
                function (FormEvent $event){
                $s = $event->getData();
                $form = $event->getForm();

                if($s->getDatedebut() !== null){
                    $form->get('timedebut')->setData($s->getDateDebut());
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

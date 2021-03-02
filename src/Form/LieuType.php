<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('nom', TextType::class, [
                'label' => 'Nom du lieu :',
                'trim' => true,
                'required' => true,
             ])


            ->add('rue', TextType::class, [
                'label' => 'Nom de la rue :',
                'trim' => true,
                'required' => true,
            ])

            ->add('latitute', NumberType::class,[
                'label' => 'Latitude :',
                'trim' => true,
                'required' => true,
            ])

            ->add('longitude' , NumberType::class,[
                'label' => 'Longitude :',
                'trim' => true,
                'required' => true,
            ])
            ->add('ville', EntityType::class,[
                'class' => Ville::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}

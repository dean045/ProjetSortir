<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class InscriptionUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('username', TextType::class,[
                'label' => 'Pseudo :',
                'trim' => true
            ])

            ->add('plainPassword', PasswordType::class,[
                'mapped' => true,
                'label' => 'Mot de passe :'
            ])

            ->add('nom', TextType::class,[
                'label' => 'Nom :',
                'trim' => true
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prénom :',
                'trim' => true
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone :',
                'trim' => true
            ])

            ->add('mail', EmailType::class, [
                'label' => 'E-Mail :',
                'trim' => true
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Site :'
            ])
            ->add('admin')
            ->add('actif')

            ->add('image', FileType::class, [
                'label' => 'Photo du profil :',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '512k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

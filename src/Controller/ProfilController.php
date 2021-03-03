<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\InscriptionUserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ProfilController extends AbstractController
{

//------------------------------DETAILS----------------------------------------------------------------------------

    /**
     * @Route(name="profil", path="user")
     */
    public function detailsUser(Request $request, EntityManagerInterface $em)
    {
        /** @var User $user */
        $user = $this->getUser();
        $etat = $em->getRepository('App:Etat')->find(1);
        $liste = $em->getRepository(Sortie::class)->getUserEtat($user, $etat);
        return $this->render('profil/index.html.twig', ['liste' => $liste]);
    }


    /**
     * @Route(name="profilAutreUser", path="profiluser/{username}", requirements={"username": "\w+"}, methods={"GET"})
     */
    public function detailsOtherUsers(Request $request, EntityManagerInterface $em)
    {
        $username = $request->get('username');
        $user = $em->getRepository('App:User')->findOneBy(["username" => $username]);
        return $this->render('profil/profilAutreUser.html.twig', ['user' => $user]);
    }

//------------------------------UPDATE----------------------------------------------------------------------------

    /**
     * @Route("/user/update", name="modification", requirements={"id": "\d+"})
     */
    public function update(ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        /** @var User $user */
        $user = $this->getUser();
        $editUser = $entityManager->getRepository('App:User')->find($user->getId());
        $cloneuser = new User();
        $cloneuser->setUsername($editUser->getUsername());
        $cloneuser->setNom($editUser->getNom());
        $cloneuser->setPrenom($editUser->getPrenom());
        $cloneuser->setTelephone($editUser->getTelephone());
        $cloneuser->setMail($editUser->getMail());
        $cloneuser->setAdmin($editUser->getAdmin());
        $cloneuser->setActif($editUser->getActif());
        $form = $this->createForm(InscriptionUserType::class, $cloneuser);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (true) {
                $imageFile = $form->get('image')->getData();
                $pseudo = $form->get('username')->getData();
                $mail = $form->get('mail')->getData();
                $oldimage = $user->getImage();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $user->setImage($imageFileName);
                }
                if($imageFile && $oldimage) $oldimage = $fileUploader->deleteFile($_SERVER['DOCUMENT_ROOT'] . "\uploads\images/" . $oldimage);
                //dd($user);
                $checkid = (($entityManager->getRepository(User::class)->findOneBy(['username' => $pseudo])) && $cloneuser->getUsername() == $editUser->getUsername()) || !$entityManager->getRepository(User::class)->findOneBy(['username' => $pseudo]);

                $checkmail = (($entityManager->getRepository(User::class)->findOneBy(['mail' => $mail])) && $cloneuser->getMail() == $editUser->getMail()) || !$entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);
                //dd($checkmail);
                if (!$checkid) {
                    //dd($editUser);
                    $this->addFlash('warning', 'Attention votre profil n\'a pas été modifié, veuillez choisir un autre pseudo !');
                    return $this->render('modification/index.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                if (!$checkmail) {
                    //dd($editUser);
                    $this->addFlash('warning', 'Attention votre profil n\'a pas été modifié, veuillez choisir un autre mail !');
                    return $this->render('modification/index.html.twig', [
                        'form' => $form->createView(),
                    ]);
                } else {
                    if ($cloneuser->getPlainPassword() && $cloneuser->getPlainPassword() != $editUser->getPlainPassword() && strlen($cloneuser->getPlainPassword()) > 7 ) {
                        $editUser->setPlainPassword($form->get('plainPassword')->getData());
                        $editUser->setPassword(
                            $passwordEncoder->encodePassword(
                                $editUser,
                                $cloneuser->getPlainPassword()
                            )
                        );
                        if ($cloneuser->getUsername())
                            $editUser->setUsername($cloneuser->getUsername());
                        if ($cloneuser->getNom())
                            $editUser->setNom($cloneuser->getNom());
                        if ($cloneuser->getPrenom())
                            $editUser->setPrenom($cloneuser->getPrenom());
                        if ($cloneuser->getTelephone())
                            $editUser->setTelephone($cloneuser->getTelephone());
                        $editUser->setMail($cloneuser->getMail());
                        $editUser->setAdmin($cloneuser->getAdmin());
                        $editUser->setActif($cloneuser->getActif());
                        $entityManager->flush();
                    } else{
                        $this->addFlash('warning', 'Attention votre profil n\'a pas été modifié, veuillez saisir votre mot de passe ou un nouveau mot de passe avec 8 caractères minimum !');
                        return $this->render('modification/index.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }

                }

                $this->addFlash('success', 'Vote profil a été mise à jour !');
                return $this->redirectToRoute('profil');
            } else {
                $this->addFlash('warning', 'Attention votre profil n\'a pas été modifié !');
            }
        }

        return $this->render('modification/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}



<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionUserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProfilController extends AbstractController
{

//------------------------------DETAILS----------------------------------------------------------------------------

    /**
     * @Route(name="profil", path="user")
     */
    public function detailsUser()
    {
        return $this->render('profil/index.html.twig');
    }


    /**
     * @Route(name="profilAutreUser", path="profiluser/{username}", requirements={"username": "\w+"}, methods={"GET"})
     */
    public function detailsOtherUsers(Request $request, EntityManagerInterface $em)
    {
        $username = $request->get('username');
        $user = $em -> getRepository('App:User')->findOneBy(["username" => $username]);
        return $this->render('profil/profilAutreUser.html.twig', ['user' => $user]);
    }

//------------------------------UPDATE----------------------------------------------------------------------------

    /**
     * @Route("/user/update", name="modification", requirements={"id": "\d+"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        /** @var User $user */
        $user = $this->getUser();
        $editUser = $entityManager->getRepository('App:User')->find($user->getId());
        $form = $this->createForm(InscriptionUserType::class, $editUser);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()){

                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $user->setImage($imageFileName);
                }
                $user = $editUser;
                //dd($user);
                $entityManager->flush();

                $this->addFlash('success', 'Vote profil a été mise à jour !');



                return $this->redirectToRoute('liste');
            }else{
                $this->addFlash('warning', 'Attention votre profil n\'a pas été modifié !');
            }
        }

        return $this->render('modification/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}



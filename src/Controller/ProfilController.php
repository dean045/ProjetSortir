<?php

namespace App\Controller;

use App\Form\InscriptionUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProfilController extends AbstractController
{

//------------------------------DETAILS----------------------------------------------------------------------------

    /**
     * @Route("user", name="profil")
     */
    public function details()
    {
        return $this->render('profil/index.html.twig');
    }

//------------------------------UPDATE----------------------------------------------------------------------------

    /**
     * @Route("/user/update", name="modification", requirements={"id": "\d+"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(InscriptionUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()){
                $entityManager->persist($user);
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


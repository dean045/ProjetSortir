<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

//-----------------------CREATE----+------AFFICHAGE----------------------------------------

    /**
     * @Route(path="/ville", name="ville", methods={"GET", "POST"})
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */

    public function index(Request $request, EntityManagerInterface $em)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();

            $ville = $em -> getRepository('App:Ville')->findtrie();
            $ville2 = new Ville();
            $form = $this->createForm(VilleType::class, $ville2);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ville2 = $form->getData();
                $em->persist($ville2);
                $em->flush();

                $this->addFlash('success', 'Votre ville a bien été ajoutée !');

                return $this->redirectToRoute('ville');
            }

            return $this->render('ville/index.html.twig', [
                'ville' => $ville,'form' => $form->createView()]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }

    }

//-----------------------------------UPDATE--------------------------------------------------

    /**
     * @Route("ville/update/{id}", name="update", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, Ville $ville, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();

            $form = $this->createForm(Villetype::class, $ville);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()){
                    $entityManager->flush();

                    $this->addFlash('success', 'La ville a bien été modifiée !');

                    return $this->redirectToRoute('ville');
                }else{
                    $this->addFlash('warning', 'Attention la ville n\'a pas été modifiée !');
                }
            }

            return $this->render('ville/update.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

//----------------------------------DELETE-----------------------------------------------------


    /**
     * @Route("ville/delete/{id}", name="delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Ville $ville, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($ville);

        $entityManager->flush();

        $this->addFlash('success', sprintf('La ville "%s" a été supprimée !', $ville->getNom()));

        return $this->redirectToRoute('ville');
    }
}

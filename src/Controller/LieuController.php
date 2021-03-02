<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route(path="/ajout_lieu", name="ajout_lieu", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */

    public function index(Request $request, EntityManagerInterface $em)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();

            $lieu1 = $em -> getRepository('App:Lieu')->findAll();
            $lieu2 = new Lieu();
            $form = $this->createForm(LieuType::class, $lieu2);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $lieu2 = $form->getData();
                $em->persist($lieu2);
                $em->flush();

                $this->addFlash('success', 'Votre lieu a bien été ajoutée !');

                return $this->redirectToRoute('creersortie');
            }

            return $this->render('lieu/index.html.twig', [
                'lieu' => $lieu1,'form' => $form->createView()]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }

    }


    /**
     * @Route("lieu/update/{id}", name="update_lieu", requirements={"id": "\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function update(Request $request, Lieu $lieu, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();

            $form = $this->createForm(Lieutype::class, $lieu);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()){
                    $entityManager->flush();

                    $this->addFlash('success', 'Le lieu a bien été modifiée !');

                    return $this->redirectToRoute('ajout_lieu');
                }else{
                    $this->addFlash('warning', 'Attention le lieu n\'a pas été modifiée !');
                }
            }

            return $this->render('lieu/update.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }


    /**
     * @Route("lieu/delete/{id}", name="delete_lieu")
     * @IsGranted("ROLE_USER")
     */
    public function delete(Lieu $lieu, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($lieu);

        $entityManager->flush();

        $this->addFlash('success', sprintf('Le lieu %s a été supprimée !', $lieu->getNom()));

        return $this->redirectToRoute('ajout_lieu');
    }
}

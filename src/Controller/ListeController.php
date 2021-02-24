<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListeController extends AbstractController
{
    /**
     * @Route(path="", name="liste", methods={"GET"})
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $liste = $em -> getRepository('App:Sortie')->findAll();
            return $this->render('liste/index.html.twig', [
                'liste' => $liste,]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route(path="/tri", name="tri", methods={"POST"})
     */
    public function tri(Request $request, EntityManagerInterface $entityManager)
    {

        return $this->redirectToRoute('app_login');
    }


}

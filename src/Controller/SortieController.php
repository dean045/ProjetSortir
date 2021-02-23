<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="Sortie/")
 */
class SortieController extends AbstractController
{
    /**
     * @Route(path="Creer", name="creersortie")
     */
    public function index(EntityManagerInterface $em)
    {
        return $this->render('sortie/creersortie.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    public function add()
    {
        //TODO : ajouter le formulaire
        $sortie = new Sortie();
        return $this->render('sortie/creersortie.html.twig', [
            'controller_name' => 'SortieController',
            ]);


    }


}

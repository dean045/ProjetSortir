<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sortie;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
            /** @var \App\Entity\User $user */
            $user= $this->getUser();
            $etats = $em ->getRepository('App:Etat')->findAll();
            $sites = $em ->getRepository('App:Site')->findAll();
            $liste = $em -> getRepository('App:Sortie')->getpublie();
            foreach ($liste as $sortie)
            {
                $now = new \DateTime('NOW',new \DateTimeZone('Europe/Paris'));
                if($sortie->getDateLimiteInscription() < $now)
                {
                    $sortie->setEtat($etats[2]);
                }
                $fin= clone $sortie->getDatedebut();
                $fin->add(new \DateInterval("PT{$sortie->getDuree()}H"));
                $fin->setTimezone(new \DateTimeZone('Europe/Paris'));
                if(($sortie->getDatedebut() < $now)  && ($now < $fin))
                {
                    $sortie->setEtat($etats[3]);
                }
                if($now > $fin)
                {
                    $sortie->setEtat($etats[4]);
                }
                if($now > $fin->add(new \DateInterval("P30D")))
                {
                    $sortie->setEtat($etats[6]);
                }

                $em->flush();
            }
            return $this->render('liste/index.html.twig', [
                'liste' => $liste,'sites'=>$sites]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route(path="/{id}",requirements={"id": "\d+"}, name="tri", methods={"GET"})
     */
    public function tri(Request $request, EntityManagerInterface $em)
    {
        if ($this->isGranted('ROLE_USER')) {
            /** @var \App\Entity\User $user */
            $user= $this->getUser();
            $id = $request ->get('id');
            $site = $em->getRepository('App:Site')->findBy(['id'=>$id]);
            $sites = $em ->getRepository('App:Site')->findAll();
            $liste = $em -> getRepository('App:Sortie')->findBy(['site'=> $site]);
            return $this->render('liste/index.html.twig', [
                'liste' => $liste,'sites'=>$sites]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("sortie/delete/{id}", name="sortie_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Sortie $sortie, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($sortie);

        $entityManager->flush();

        $this->addFlash('success', sprintf('La ville "%s" a été supprimée !', $sortie->getNom()));

        return $this->redirectToRoute('liste');
    }

}

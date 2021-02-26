<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortirController extends AbstractController
{
    /**
     * @Route(path="/participer/{id}", name="particper", requirements={"id":"\d+"},methods={"GET","POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $id = $request->get('id');
        $user = $this->getUser();
        /** @var \App\Entity\Sortie $sortie */
        $sortie = $entityManager->getRepository('App:Sortie')->findOneBy(['id'=>$id]);
        $datenow = new \DateTime("now");
        if($sortie->getDateLimiteInscription() < $datenow)
        {
            $this->addFlash('error', 'Les inscriptions sont clos pour cette sortie.');
        }
        else
        {
            $sortie->addParticipant($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();
        }
        return $this->redirectToRoute('liste');
    }
    /**
     * @Route(path="/sedesister/{id}", name="sedesister", requirements={"id":"\d+"},methods={"GET","POST"})
     */
    public function seDesister(Request $request, EntityManagerInterface $entityManager)
    {
        $id = $request->get('id');
        $user = $this->getUser();
        $sortie = $entityManager->getRepository('App:Sortie')->findOneBy(['id'=>$id]);
        $sortie->removeParticipant($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();
        return $this->redirectToRoute('liste');
    }
}

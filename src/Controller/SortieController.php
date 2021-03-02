<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(path="sortie/")
 */
class SortieController extends AbstractController
{

    /**
     * @Route(path="creer", name="creersortie", methods={"GET", "POST"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        // Initialisation de l'objet mappé au formulaire
        $sortie = new Sortie();

        // Initialisation des dates et heures à maintenant
        $sortie->setDateDebut(new \DateTime());

        $sortie->setDateLimiteInscription(new \DateTime());

        // ID & site rattaché de l'user
        $sortie->setOrganisateur($this->getUser());
        $user = $this->getUser();
        $sortie->setSite($user->getSite());
        $lieux= $em->getRepository('App:Lieu')->findAll();
        // Création du formulaire
        $form = $this->createForm('App\Form\SortieType', $sortie);

        // Récup des données de la requête HTTP au formulaire
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $sortie->setSite($user->getSite());
            if ($form->getClickedButton() === $form->get('Publier')) {
                $etat = $em->getRepository('App:Etat')->findOneBy(['id' => 2]);
                $msg = 'Votre sortie a été postée avec succès!';
            } else {
                $etat = $em->getRepository('App:Etat')->findOneBy(['id' => 1]);
                $msg = 'Votre brouillon sortie a été enregistré avec succès!';
            }
            $sortie->setEtat($etat);
            $sortie = $form->getData();
            // Insertion de l'objet en BDD
            $em->persist($sortie);

            // Validation de la transaction
            $em->flush();

            // Ajout d'un message de confirmation
            $this->addFlash('success', $msg);

            //Redirection sur la page de détails
            return $this->redirectToRoute('detailsortie', ['id' => $sortie->getId()]);
        }

        // Affichage du formulaire
        return $this->render('sortie/creersortie.html.twig', [
            'SortieForm' => $form->createView(), 'lieu' => $lieux
        ]);
    }

    /**
     * @Route(name="detailsortie", path="detailsortie/{id}", requirements={"id": "\d+"}, methods={"GET"})
     */
    public function details(Request $request, EntityManagerInterface $em)
    {
        $id = $request->get('id');

        $sortie = $em->getRepository('App:Sortie')->findOneBy(["id" => $id]);

        return $this->render('sortie/detailsortie.html.twig', ['sortie' => $sortie]);
    }

    /**
     * @Route(name="modifiersortie", path="modifiersortie/{id}", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em)
    {
        $id = $request->get('id');
        $sortie = $em->getRepository('App:Sortie')->findOneBy(["id" => $id]);
        $form = $this->createForm(SortieType::class, $sortie);
        $lieux= $em->getRepository('App:Lieu')->findAll();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $sortie = $form->getData();
            $em->flush();
            $this->addFlash('Success', 'Votre sortie a été modifiée avec succès');
            return $this->redirectToRoute('detailsortie', ['id' => $sortie->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('Warning', 'Attention, les modifications effectuées à votre sortie n\'ont pas été effectuées!');
        }
        return $this->render('sortie/modifiersortie.html.twig', ['SortieForm' => $form->createView(), 'sortie' => $sortie, 'lieu' => $lieux]);
    }

    /**
     * @Route(name="annulersortie", path="annulersortie/{id}", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function cancel(Request $request, EntityManagerInterface $em)
    {
        $id = $request->get('id');
        $sortie = $em->getRepository('App:Sortie')->findOneBy(["id" => $id]);
        $etat = $em->getRepository('App:Etat')->findOneBy(["id" => 6]);
        $sortie -> setEtat($etat);
        $em->flush();
        $this->addFlash('Success', 'Votre sortie a été annulée avec succès');
        return $this->redirectToRoute('detailsortie', ['id' => $sortie->getId()]);
    }

    /**
     * @Route(name="lieu", path="lieu", methods={"GET", "POST"})
     */
    public function getlieu(Request $request, LieuRepository $repository, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');

            $lieu = $repository->find((int) $id);

            $json = $serializer->serialize($lieu, 'json',['groups' => ['liste']]);
            return new JsonResponse($json, 200, [], true);
        }
    }
}

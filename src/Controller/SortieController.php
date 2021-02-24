<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="sortie/")
 */
class SortieController extends AbstractController
{

    /**
     * @Route(path="creer", name="creersortie", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        // Initialisation de l'objet mappé au formulaire
        $sortie = new Sortie();

        // Initialisation des dates et heures à maintenant
        $sortie->setDateDebut(new \DateTime());

        $sortie->setDateLimiteInscription(new \DateTime());

        // ID de l'user
        $sortie->setOrganisateur($this->getUser());

        // Création du formulaire
        $form = $this->createForm('App\Form\SortieType', $sortie);

        // Récup des données de la requête HTTP au formulaire
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $sortie = $form->getData();
/*
            $datedebut = $form->get('datedebut')->getData();
            $heuredebut = $form->get('timedebut')->getData();
            $datelimite = $form->get('dateLimiteInscription')->getData();
            $heurelimite = $form->get('timelimite')->getData();

            $hdebut = $heuredebut->format('H');
            $idebut = $heuredebut->format('i');
            $hlimite = $heurelimite->format('H');
            $ilimite = $heurelimite->format('i');

            $datedebut->setTime($hdebut, $idebut, 0);
            $datelimite->setTime($hlimite, $ilimite, 0);

            $sortie->setDatedebut($datedebut);
            $sortie->setDateLimiteInscription($datelimite);
*/

            // Insertion de l'objet en BDD
            $em->persist($sortie);

            // Validation de la transaction
            $em->flush();

            // Ajout d'un message de confirmation
            $this->addFlash('success', 'Votre sortie a été postée avec succès!');

            //Redirection sur la page de détails
            return $this->render('sortie/detailsortie.html.twig', ['sortie' => $sortie ]);
        }

        // Affichage du formulaire
        return $this->render('sortie/creersortie.html.twig', [
            'SortieForm' => $form->createView()
        ]);
    }

        /**
         * @Route(name="detailsortie", path="detailsortie/{id}", requirements={"id": "\d+"}, methods={"GET"})
         */
        public function details(Request $request, EntityManagerInterface $em)
        {
            $id = $request -> get('id');

            $sortie = $em -> getRepository('App:Sortie')->findOneBy(["id"=>$id]);

            return $this->render('sortie/detailsortie.html.twig', ['sortie' => $sortie]);
        }

        /**
         * @Route(name="modifiersortie", path="detailsortie/modifiersortie/{id}", requirements={"id": "\d+"}, methods={"GET", "POST"})
         * @IsGranted("ROLE_ADMIN")
         */
        public function edit(Request $request, EntityManagerInterface $em)
        {
            $id = $request -> get('id');
            $sortie = $em -> getRepository('App:Sortie') -> findOneBy(["id"=>$id]);
            $form = $this -> createForm(SortieType::class, $sortie);
            $form -> handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $sortie = $form->getData();
                /*
                $datedebut = $form->get('datedebut')->getData();
                $heuredebut = $form->get('timedebut')->getData();
                $datelimite = $form->get('dateLimiteInscription')->getData();
                $heurelimite = $form->get('timelimite')->getData();

                $hdebut = $heuredebut->format('H');
                $idebut = $heuredebut->format('i');
                $hlimite = $heurelimite->format('H');
                $ilimite = $heurelimite->format('i');

                $datedebut->setTime($hdebut, $idebut, 0);
                $datelimite->setTime($hlimite, $ilimite, 0);

                $sortie->setDatedebut($datedebut);
                $sortie->setDateLimiteInscription($datelimite);
*/
                $em -> flush();
                $this -> addFlash('Success', 'Votre sortie a été modifiée avec succès');
                return $this -> redirectToRoute('detailsortie', ['id' => $sortie -> getId()]);
            } else {
                $this -> addFlash('Warning', 'Attention, les modifications effectuées à votre sortie n\'ont pas été effectuées!');
            }
            return $this -> render('sortie/modifiersortie.html.twig', ['SortieForm' => $form -> createView()]);
        }
}

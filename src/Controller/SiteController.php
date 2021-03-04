<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route(path="/site", name="site", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */

    public function index(Request $request, EntityManagerInterface $em)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();

            $site = $em -> getRepository('App:Site')->findtrie();
            $site2 = new Site();
            $form = $this->createForm(SiteType::class, $site2);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $site2 = $form->getData();
                $em->persist($site2);
                $em->flush();

                $this->addFlash('success', 'Votre site a bien été ajouté !');

                return $this->redirectToRoute('site');
            }

            return $this->render('site/index.html.twig', [
                'site' => $site,'form' => $form->createView()]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }

    }

    //-----------------------------------UPDATE--------------------------------------------------

    /**
     * @Route("site/update/{id}", name="site_update", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, Site $site, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();

            $form = $this->createForm(Sitetype::class, $site);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()){
                    $entityManager->flush();

                    $this->addFlash('success', 'Le site a bien été modifié !');

                    return $this->redirectToRoute('site');
                }else{
                    $this->addFlash('warning', 'Attention le site n\'a pas été modifié !');
                }
            }

            return $this->render('site/update.html.twig', [
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
     * @Route("site/delete/{id}", name="site_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Site $site, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($site);

        $entityManager->flush();

        $this->addFlash('success', sprintf('Le site "%s" a été supprimé !', $site->getNom()));

        return $this->redirectToRoute('site');
    }
}

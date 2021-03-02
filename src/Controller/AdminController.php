<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $liste = $em->getRepository(User::class)->findAll();


        return $this->render('admin/index.html.twig', [
            'liste' => $liste,
        ]);
    }


    /**
     * @Route("/admin_activer", name="admin_activer", methods={"GET","POST"})
     */
    public function activer(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isXmlHttpRequest()) {
            $liste = $request->request->get('liste');
            $compteur = 0;

            foreach ($liste as $id)
            {
                $user = $em->getRepository(User::class)->find($id);
                if($user->getActif() == false)
                {
                    $user ->setActif(true);
                    $em->flush();
                    $compteur++;
                }
            }
            return new JsonResponse($compteur, 200, [], true);
        }
    }

    /**
     * @Route("/admin_desactiver", name="admin_desactiver", methods={"GET","POST"})
     */
    public function desactiver(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isXmlHttpRequest()) {
            $liste = $request->request->get('liste');
            $compteur = 0;

            foreach ($liste as $id)
            {
                $user = $em->getRepository(User::class)->find($id);
                if($user->getActif() == true)
                {
                    $user ->setActif(false);
                    $em->flush();
                    $compteur++;
                }
            }
            return new JsonResponse($compteur, 200, [], true);
        }
    }

    /**
     * @Route("/admin_suppr", name="admin_suppr", methods={"GET","POST"})
     */
    public function suppr(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isXmlHttpRequest()) {
            $liste = $request->request->get('liste');
            $compteur = 0;

            foreach ($liste as $id)
            {
                $user = $em->getRepository(User::class)->find($id);
                if($user)
                {
                    $em->remove($user);
                    $em->flush();
                    $compteur++;
                }
            }
            return new JsonResponse($compteur, 200, [], true);
        }
    }
}

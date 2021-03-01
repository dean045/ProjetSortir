<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionUserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param $newFilename
     * @return Response
     */
    public function index(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader): Response
    {

        $editUser = new User();

        $form = $this->createForm(InscriptionUserType::class, $editUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $editUser->setImage($imageFileName);
            }

            $editUser->setPassword(
                $passwordEncoder->encodePassword(
                    $editUser,
                    $editUser->getPlainPassword()
                )
            );
            if($form->get('admin') == true)
            {
                $editUser->setRoles(["ROLE_ADMIN"]);
            }
            $editUser = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($editUser);
            $em->flush();

            return $this->redirectToRoute('liste');
        }

        return $this->render('inscription/index.html.twig', ['form' => $form->createView(),]);
    }
}

<?php

namespace App\Controller;

use App\Service\FileUploader;
use League\Csv\Reader;
use App\Entity\User;
use App\Form\InscriptionUserType;
use App\Form\UploadType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return Response
     */
    public function index(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $editUser = clone $user;
        $form = $this->createForm(InscriptionUserType::class, $editUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $editUser->setPassword(
                $passwordEncoder->encodePassword(
                    $editUser,
                    $editUser->getPlainPassword()
                )
            );
            if ($form->get('admin') == true) {
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

    /**
     * @Route("/upload", name="upload", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function upload(EntityManagerInterface $em, Request $request, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get("import")->getData();
            $file = $fileUploader->upload($file);
            dd($file);

            $csv = Reader::createFromPath($file->getClientOriginalName())
                ->setHeaderOffset(0);

            $rowNo = 1;

            return $this->redirectToRoute('liste');
        }

        return $this->render('inscription/index.html.twig', ['form' => $form->createView(),]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionUserType;
use App\Form\UploadType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

            $editUser->setRoles(["ROLE_USER"]);
            $editUser->setActif(true);
            $editUser->setAdmin(false);

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
    public function upload(EntityManagerInterface $em, Request $request, ValidatorInterface $validator, FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get("import")->getData();
            $file = $fileUploader->upload($file);
            $ligne = 0;


            $csv = Reader::createFromPath($_SERVER['DOCUMENT_ROOT'] . "\uploads\images/" . $file)
                ->setHeaderOffset(0);

            foreach ($csv as $record) {
                $user = new User();
                $user->setUsername($record['username']);
                $user->setPlainPassword($record['password']);
                $user->setNom($record['nom']);
                $user->setPrenom($record['prenom']);
                $user->setTelephone($record['telephone']);
                $user->setMail($record['mail']);
                $user->setActif(true);
                $user->setAdmin(false);
                $user->setImage("aucune");
                $site = $em->getRepository('App:Site')->findOneBy(['nom' => $record['site']]);
                $user->setSite($site);
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $user->getPlainPassword()
                    )
                );
                $errors = $validator->validate($user);

                if (count($errors) == 0) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                } else {
                    $this->addFlash('warning', "L'ajout d'utilisateur s'est arrété à la ligne " . $ligne . " car les données saisies ne sont pas valides.");
                    $file = $fileUploader->deleteFile($_SERVER['DOCUMENT_ROOT'] . "\uploads\images/" . $file);
                    return $this->redirectToRoute('admin');
                }
                $ligne++;
            }
            $this->addFlash('success', $ligne . " utilisateurs ont été inscrit.");
            $file = $fileUploader->deleteFile($_SERVER['DOCUMENT_ROOT'] . "\uploads\images/" . $file);
            return $this->redirectToRoute('admin');
        }

        return $this->render('inscription/upload.html.twig', ['form' => $form->createView(),]);
    }
}

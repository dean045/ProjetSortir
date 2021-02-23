<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for($i=0; $i<10; $i++)
        {
            $user = new User();
            $user->setUsername("user" . $i)
                ->setRoles(["ROLE_USER"]);
            $password = $this->encoder->encodePassword($user, '12345678');
            $user->setPassword($password)
                ->setNom("Dupond")
                ->setPrenom("Jean")
                ->setTelephone("0102030405")
                ->setMail("test@test.fr")
                ->setAdmin(0)
                ->setActif(1);
            $manager->persist($user);
        }
        $manager->flush();
    }
}

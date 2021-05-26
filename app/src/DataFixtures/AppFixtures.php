<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Role;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;




    
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
    }





    public function load(ObjectManager $manager)
    {
        $admin = new User;
        $admin->setEmail("admin@app.com");
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, "password"));
        $admin->setRoles([Role::ADMIN]);

        $manager->persist($admin);
        $manager->flush();
    }
}

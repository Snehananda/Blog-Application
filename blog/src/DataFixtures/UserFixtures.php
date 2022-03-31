<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public $userManager;
    
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName("Sneha");
        $user->setEmail("sneha@gmail.com");
        $user->setPassword("sneha");
        $manager->persist($user);

        $manager->flush();

        $userManager = $user;
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManager;

class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $blog = new Blog();
        $blog->setTitle("Python");
        $blog->setContent("Python is very easy");
        $date = new \DateTime('@'.strtotime('now'));
        $blog->setDate($date);
        //$blog->setUser($user);
        //var_dump($user_id);
        //$blog->setUser($em->getReference('User\Bundle\RegisterBundle\Entity\User', $user_id));
        $manager->flush();
    }
}

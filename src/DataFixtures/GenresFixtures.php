<?php

namespace App\DataFixtures;

use App\Entity\Genres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenresFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $action = new Genres();
        $action->setName('Action');
        $action->setCreated(new \DateTime());
        $action->setUpdated(new \DateTime());
        $manager->persist($action);

        $comedy = new Genres();
        $comedy->setName('Comedy');
        $comedy->setCreated(new \DateTime());
        $comedy->setUpdated(new \DateTime());
        $manager->persist($comedy);

        $drama = new Genres();
        $drama->setName('Drama');
        $drama->setCreated(new \DateTime());
        $drama->setUpdated(new \DateTime());
        $manager->persist($drama);

        $manager->flush();


        // $manager->flush();

        // $this->addReference('genres_1', $genres);
        // $this->addReference('genres_2', $genres2);
        // $this->addReference('genres_3', $genres3);
        // $this->addReference('genres_4', $genres4);
        // $this->addReference('genres_5', $genres5);
    }
}

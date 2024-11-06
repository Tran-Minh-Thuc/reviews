<?php

namespace App\DataFixtures;

use App\Entity\Customers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $john = new Customers();
        $john->setName('John Doe');
        $john->setEmail('john.doe@example.com');
        $john->setImg('path/to/john.jpg');
        $john->setPhone('123-456-7890');
        $john->setStatus(1);
        $john->setCreated(new \DateTime());
        $john->setUpdated(new \DateTime());
        $manager->persist($john);

        $jane = new Customers();
        $jane->setName('Jane Smith');
        $jane->setEmail('jane.smith@example.com');
        $jane->setImg('path/to/jane.jpg');
        $jane->setPhone('123-456-7891');
        $jane->setStatus(1);
        $jane->setCreated(new \DateTime());
        $jane->setUpdated(new \DateTime());
        $manager->persist($jane);

        $alice = new Customers();
        $alice->setName('Alice Brown');
        $alice->setEmail('alice.brown@example.com');
        $alice->setImg('path/to/alice.jpg');
        $alice->setPhone('123-456-7892');
        $alice->setStatus(1);
        $alice->setCreated(new \DateTime());
        $alice->setUpdated(new \DateTime());
        $manager->persist($alice);
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Movies;
use App\Entity\Reviews;
use App\Entity\Customers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $movieRepo = $manager->getRepository(MovieS::class);
        $customerRepo = $manager->getRepository(Customers::class);

        $review1 = new Reviews();
        $review1->setReviewtext('Amazing movie with great plot and characters!');
        $review1->setRating(5);
        $review1->setCreated(new \DateTime());
        $review1->setUpdated(new \DateTime());
        $review1->setMovie($movieRepo->findOneBy(['title' => 'The Shawshank Redemption']));
        $review1->setCustomer($customerRepo->findOneBy(['name' => 'John Doe']));
        $manager->persist($review1);

        $review2 = new Reviews();
        $review2->setReviewtext('Enjoyed every moment of this masterpiece.');
        $review2->setRating(5);
        $review2->setCreated(new \DateTime());
        $review2->setUpdated(new \DateTime());
        $review2->setMovie($movieRepo->findOneBy(['title' => 'The Godfather']));
        $review2->setCustomer($customerRepo->findOneBy(['name' => 'Jane Smith']));
        $manager->persist($review2);

        $review3 = new Reviews();
        $review3->setReviewtext('Could have been better, but overall worth watching.');
        $review3->setRating(4);
        $review3->setCreated(new \DateTime());
        $review3->setUpdated(new \DateTime());
        $review3->setMovie($movieRepo->findOneBy(['title' => 'The Dark Knight']));
        $review3->setCustomer($customerRepo->findOneBy(['name' => 'Alice Brown']));
        $manager->persist($review3);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            MoviesFixtures::class,
            CustomerFixtures::class,
        ];
    }
}

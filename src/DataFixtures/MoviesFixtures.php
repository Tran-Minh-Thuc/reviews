<?php

namespace App\DataFixtures;

use App\Entity\Movies;
use App\Entity\Genres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MoviesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $genreRepo = $manager->getRepository(Genres::class);

        $shawshank = new Movies();
        $shawshank->setTitle('The Shawshank Redemption');
        $shawshank->setDescription('A man imprisoned for a crime he didn’t commit finds solace and eventual redemption.');
        $shawshank->setImg('path/to/shawshank.jpg');
        $shawshank->setCreated(new \DateTime());
        $shawshank->setUpdated(new \DateTime());
        $shawshank->setGenre($genreRepo->findOneBy(['name' => 'Drama']));
        $manager->persist($shawshank);

        $godfather = new Movies();
        $godfather->setTitle('The Godfather');
        $godfather->setDescription('The story of a powerful Italian-American crime family.');
        $godfather->setImg('path/to/godfather.jpg');
        $godfather->setCreated(new \DateTime());
        $godfather->setUpdated(new \DateTime());
        $godfather->setGenre($genreRepo->findOneBy(['name' => 'Drama']));
        $manager->persist($godfather);

        $darkKnight = new Movies();
        $darkKnight->setTitle('The Dark Knight');
        $darkKnight->setDescription('Batman faces the Joker, a new threat to Gotham.');
        $darkKnight->setImg('path/to/dark_knight.jpg');
        $darkKnight->setCreated(new \DateTime());
        $darkKnight->setUpdated(new \DateTime());
        $darkKnight->setGenre($genreRepo->findOneBy(['name' => 'Action']));
        $manager->persist($darkKnight);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            GenresFixtures::class,
        ];
    }
}

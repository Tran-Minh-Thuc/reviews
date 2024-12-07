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
        $shawshank->setDescription('The Shawshank Redemption (1994) follows Andy Dufresne, a man wrongly imprisoned for his wife’s murder, as he forms a friendship with fellow inmate Red at Shawshank prison. Over the years, Andy uses his financial skills to navigate prison life, while secretly planning his escape. The film explores themes of hope, friendship, and redemption, and is widely considered one of the greatest films of all time.');
        $shawshank->setImg('data:image/jpeg;base64,' . base64_encode(file_get_contents('public/images/shawshank.jpg')));

        $shawshank->setCreated(new \DateTime());
        $shawshank->setUpdated(new \DateTime());
        $shawshank->setGenre($genreRepo->findOneBy(['name' => 'Drama']));
        $manager->persist($shawshank);

        $godfather = new Movies();
        $godfather->setTitle('The Godfather');
        $godfather->setDescription('The Godfather (1972), directed by Francis Ford Coppola, is a classic crime drama based on Mario Puzo s novel. The story follows the powerful Corleone family, led by patriarch Vito Corleone (Marlon Brando), who controls a vast Mafia empire in New York. When Vito is attacked, his youngest son, Michael (Al Pacino), who initially wants to avoid the family business, becomes increasingly involved in the violent world of organized crime. As Michael rises to power, he navigates betrayal, loyalty, and the heavy burden of family legacy. The film is renowned for its iconic performances, intricate storytelling, and exploration of power, loyalty, and morality.');
        $godfather->setImg('data:image/jpeg;base64,' . base64_encode(file_get_contents('public/images/godfather.jpg')));
        $godfather->setCreated(new \DateTime());
        $godfather->setUpdated(new \DateTime());
        $godfather->setGenre($genreRepo->findOneBy(['name' => 'Drama']));
        $manager->persist($godfather);

        $darkKnight = new Movies();
        $darkKnight->setTitle('The Dark Knight');
        $darkKnight->setDescription('The Dark Knight (2008), directed by Christopher Nolan, is a superhero film and the second installment in his The Dark Knight Trilogy. The film follows Batman (Christian Bale) as he battles the Joker (Heath Ledger), a sadistic criminal mastermind who seeks to plunge Gotham City into anarchy. As the Joker pushes Batman to his limits, the hero grapples with his own morals, identity, and the consequences of his vigilante justice. The film explores themes of chaos, heroism, and the fine line between good and evil. The Dark Knight is celebrated for its intense performances, especially Ledger’s portrayal of the Joker, and its influence on the superhero genre.');
        $darkKnight->setImg('data:image/jpeg;base64,' . base64_encode(file_get_contents('public/images/darkknight.jpg')));
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

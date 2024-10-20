<?php

namespace App\Entity;

use App\Repository\ReviewsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewsRepository::class)]
class Reviews
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?movies $movie_id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?customers $customer_id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reviewtext = null;

    #[ORM\Column]
    private ?int $rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovieId(): ?movies
    {
        return $this->movie_id;
    }

    public function setMovieId(?movies $movie_id): static
    {
        $this->movie_id = $movie_id;

        return $this;
    }

    public function getCustomerId(): ?customers
    {
        return $this->customer_id;
    }

    public function setCustomerId(?customers $customer_id): static
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getReviewtext(): ?string
    {
        return $this->reviewtext;
    }

    public function setReviewtext(string $reviewtext): static
    {
        $this->reviewtext = $reviewtext;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
}

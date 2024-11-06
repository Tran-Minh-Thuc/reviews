<?php

namespace App\Controller;

use App\Entity\Movies;
use App\Form\MovieType;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;

class MovieController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/movie', name: 'movie_index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $movies = $this->em->getRepository(Movies::class)->findAll();
        $pagination = $paginator->paginate(
            $movies,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );
        return $this->render('movie/index.html.twig', [
            'movies' => $pagination,
        ]);
    }
    #[Route('/movie/create-movie', name: 'create-movie')]
    public function createMovie(Request $request): Response
    {
        $movie = new Movies();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie->setCreated(new DateTime());
            $movie->setUpdated(new DateTime());
            $this->em->persist($movie);
            $this->em->flush();

            $this->addFlash('insert_movie', 'true');
            return $this->redirectToRoute('movie_index');
        }

        return $this->render('movie/movie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit customer.
     */
    #[Route('/movie/edit-movie/{id}', name: 'edit-movie')]
    public function editMovie(Request $request, $id)
    {

        $movie = $this->em->getRepository(Movies::class)->find($id);
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $movie->setUpdated(new DateTime());
            $this->em->persist($movie);
            $this->em->flush();

            $this->addFlash('update_movie', 'true');
            return $this->redirectToRoute('movie_index');
        }
        return $this->render('movie/movie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a customer.
     */
    #[Route('/movie/delete-movie/{id}', name: 'delete-movie')]
    public function deleteMovie($id)
    {
        $movie = $this->em->getRepository(Movies::class)->find($id);
        $movie = $this->em->getRepository(Movies::class)->find($id);

        if ($movie) {
            // Xóa tất cả các review liên quan đến movie
            foreach ($movie->getReviews() as $review) {
                $this->em->remove($review);
            }

            // Xóa movie
            $this->em->remove($movie);
            $this->em->flush();

            // Thêm flash message và điều hướng lại trang danh sách phim
            $this->addFlash('delete_movie', 'true');
            return $this->redirectToRoute('movie_index');
        }
        return new Response('Invalid movie data', Response::HTTP_BAD_REQUEST);
    }

    /**
     * Search for movies.
     */
    #[Route('/movie/search-movie', name: 'search-movie')]
    public function searchMovie(Request $request): Response
    {
        $searchQuery = $request->query->get('search_query');
        $searchField = $request->query->get('search_field');
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('c')
            ->from('App\Entity\Movies', 'm');

        if ($searchField === 'title') {
            $queryBuilder
                ->andWhere("c.title LIKE :searchQuery")
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }
        $Movies = $queryBuilder->getQuery()->getResult();
        $formattedMovies = [];
        foreach ($Movies as $m) {
            $formattedMovies[] = [
                'id' => $m->getId(),
                'img' => $m->getImg(),
                'title' => $m->getUsername(),
                'description' => $m->getEmail(),
                'created' => $m->getCreated(),
                'updated' => $m->getUpdated(),
            ];
        }
        return $this->json($formattedMovies);
    }
}

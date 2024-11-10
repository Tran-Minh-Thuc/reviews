<?php

namespace App\Controller;

use App\Entity\Genres;
use App\Form\GenresType;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class GenresController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('admin/genres', name: 'genres_index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $genres = $this->em->getRepository(Genres::class)->findAll();
        $pagination = $paginator->paginate(
            $genres,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );
        return $this->render('genres/index.html.twig', [
            'genres' => $pagination,
        ]);
    }
    #[Route('admin/genres/create-genres', name: 'create-genres')]
    public function createGenres(Request $request): Response
    {
        $genres = new Genres();
        $form = $this->createForm(GenresType::class, $genres);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $genres->setCreated(new DateTime());
            $genres->setUpdated(new DateTime());
            $this->em->persist($genres);
            $this->em->flush();

            $this->addFlash('insert_genres', 'true');
            return $this->redirectToRoute('genres_index');
        }

        return $this->render('genres/genres.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit customer.
     */
    #[Route('admin/genres/edit-genres/{id}', name: 'edit-genres')]
    public function editGenres(Request $request, $id)
    {

        $genres = $this->em->getRepository(Genres::class)->find($id);
        $form = $this->createForm(GenresType::class, $genres);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genres->setUpdated(new DateTime());
            $this->em->persist($genres);
            $this->em->flush();

            $this->addFlash('update_genres', 'true');
            return $this->redirectToRoute('genres_index');
        }
        return $this->render('genres/genres.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a customer.
     */
    #[Route('admin/genres/delete-genres/{id}', name: 'delete-genres')]
    public function deleteGenres(int $id): Response
    {
        $genres = $this->em->getRepository(Genres::class)->find($id);

        if (!$genres) {
            return new Response('Thể loại không tồn tại.', Response::HTTP_NOT_FOUND);
        }

        // Xóa liên kết thể loại khỏi tất cả các bộ phim
        foreach ($genres->getMovies() as $movie) {
            $movie->setGenre(null); // Xóa liên kết thể loại khỏi phim
        }

        // Xóa thể loại
        $this->em->remove($genres);
        $this->em->flush();

        // Thêm flash message và điều hướng
        $this->addFlash('success', 'Thể loại đã được xóa khỏi các phim liên quan.');
        return $this->redirectToRoute('genres_index');
    }

    /**
     * Search for movies.
     */
    #[Route('admin/genres/search-genres', name: 'search_genres')]
    public function searchGenres(Request $request): JsonResponse
    {
        $searchQuery = $request->query->get('search_query', '');
        $searchField = $request->query->get('search_field', 'name');

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('g')
            ->from(Genres::class, 'g');

        if (!empty($searchQuery) && $searchField === 'name') {
            $queryBuilder
                ->andWhere('g.name LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $genres = $queryBuilder->getQuery()->getResult();
        $formattedGenres = array_map(function (Genres $genre) {
            return [
                'id' => $genre->getId(),
                'name' => $genre->getName(),
                'created' => $genre->getCreated()->format('Y-m-d H:i:s'),
                'updated' => $genre->getUpdated()->format('Y-m-d H:i:s'),
            ];
        }, $genres);

        return $this->json($formattedGenres);
    }
}

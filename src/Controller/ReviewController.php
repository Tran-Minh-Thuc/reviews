<?php

namespace App\Controller;

use App\Entity\Reviews;
use App\Entity\Movies;
use App\Entity\Customers;
use App\Form\ReviewFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class ReviewController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/admin/reviews', name: 'reviews')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $reviews = $this->em->getRepository(Reviews::class)->findAll();
        $pagination = $paginator->paginate(
            $reviews,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );
        return $this->render('review/index.html.twig', [
            'reviews' => $pagination,
        ]);
    }
    #[Route('/admin/create-review', name: 'create-review')]
    public function createReview(Request $request)
    {
        $review = new Reviews();
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $review->setCreated(new DateTime());
            $review->setUpdated(new DateTime());
            $this->em->persist($review);
            $this->em->flush();
            $this->addFlash('insert_review', 'true');
            return $this->redirectToRoute('reviews');
        }
        return $this->render('review/review.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/edit-review/{id}', name: 'edit-review')]
    public function editReview(Request $request, $id)
    {
        $review = $this->em->getRepository(Reviews::class)->find($id);
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($review);
            $this->em->flush();
            $this->addFlash('update_review', 'true');
            return $this->redirectToRoute('reviews');
        }
        return $this->render('review/review.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/delete-review/{id}', name: 'delete-review')]
    public function deleteReview(Request $request, $id)
    {
        $review = $this->em->getRepository(reviews::class)->find($id);
        if ($review) {
            $this->em->remove($review);
            $this->em->flush();
            $this->addFlash('delete_review', 'true');
            return $this->redirectToRoute('reviews');
        }
        return new Response('Invalid review data', Response::HTTP_BAD_REQUEST);
    }
    #[Route('/admin/search-reviews', name: 'search-review')]
    public function searchReview(Request $request): Response
    {
        $searchQuery = $request->query->get('search_query');
        $searchField = $request->query->get('search_field');
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('r')
            ->from('App\Entity\Reviews', 'r')
            ->leftJoin('r.customer', 'u')
            ->leftJoin('r.movie', 'm');

        if ($searchField === 'rating') {
            $queryBuilder
                ->andWhere("r.$searchField = :searchQuery")
                ->setParameter('searchQuery', $searchQuery);
        } elseif ($searchField === 'customername') {
            $queryBuilder
                ->andWhere("u.name LIKE :searchQuery")
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        } elseif ($searchField === 'title') {
            $queryBuilder
                ->andWhere("m.title LIKE :searchQuery")
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }
        $Reviews = $queryBuilder->getQuery()->getResult();
        $formattedReviews = [];
        foreach ($Reviews as $review) {
            $formattedReviews[] = [
                'id' => $review->getId(),
                'reviewtext' => $review->getReviewText(),
                'rating' => $review->getRating(),
                'customer' => [
                    'id' => $review->getCustomer()->getId(),
                    'name' => $review->getCustomer()->getName()
                ],
                'movie' => [
                    'id' => $review->getMovie()->getId(),
                    'title' => $review->getMovie()->getTitle()
                ]
            ];
        }
        return $this->json($formattedReviews);
    }
}

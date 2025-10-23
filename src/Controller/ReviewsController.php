<?php

namespace App\Controller;

use App\Entity\Reviews;
use App\Form\ReviewsType;
use App\Repository\ReviewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reviews')]
final class ReviewsController extends AbstractController
{
    #[Route(name: 'app_reviews_index', methods: ['GET'])]
    public function index(ReviewsRepository $reviewsRepository): Response
    {
        return $this->render('reviews/index.html.twig', [
            'reviews' => $reviewsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reviews_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $review = new Reviews();
        
        // Définir automatiquement le client connecté
        $review->setClient($this->getUser());
        
        $form = $this->createForm(ReviewsType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_reviews_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reviews/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reviews_show', methods: ['GET'])]
    public function show(Reviews $review): Response
    {
        return $this->render('reviews/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reviews_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reviews $review, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est connecté et qu'il est le propriétaire de l'avis
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        if ($review->getClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres avis.');
        }
        
        $form = $this->createForm(ReviewsType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reviews_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reviews/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reviews_delete', methods: ['POST'])]
    public function delete(Request $request, Reviews $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reviews_index', [], Response::HTTP_SEE_OTHER);
    }
}

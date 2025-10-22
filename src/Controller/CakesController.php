<?php

namespace App\Controller;

use App\Entity\Cakes;
use App\Form\CakesType;
use App\Repository\CakesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cakes')]
final class CakesController extends AbstractController
{
    #[Route(name: 'app_cakes_index', methods: ['GET'])]
    public function index(CakesRepository $cakesRepository): Response
    {
        return $this->render('cakes/index.html.twig', [
            'cakes' => $cakesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cakes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cake = new Cakes();
        $form = $this->createForm(CakesType::class, $cake);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cake);
            $entityManager->flush();

            return $this->redirectToRoute('app_cakes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cakes/new.html.twig', [
            'cake' => $cake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cakes_show', methods: ['GET'])]
    public function show(Cakes $cake): Response
    {
        return $this->render('cakes/show.html.twig', [
            'cake' => $cake,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cakes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cakes $cake, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CakesType::class, $cake);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cakes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cakes/edit.html.twig', [
            'cake' => $cake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cakes_delete', methods: ['POST'])]
    public function delete(Request $request, Cakes $cake, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cake->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cake);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cakes_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Cakes;
use App\Form\CakesType;
use App\Repository\CakesRepository;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

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
        $user = $this->getUser();

        // ✅ Vérifie si l'utilisateur est admin
        if (!$user || !$user->isAdmin()) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        $cake = new Cakes();
        $form = $this->createForm(CakesType::class, $cake);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cake);
            $entityManager->flush();

            return $this->redirectToRoute('app_cakes_index');
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
        $user = $this->getUser();

        // ✅ Vérifie si l'utilisateur est admin
        if (!$user || !$user->isAdmin()) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        $form = $this->createForm(CakesType::class, $cake);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cakes_index');
        }

        return $this->render('cakes/edit.html.twig', [
            'cake' => $cake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cakes_delete', methods: ['POST'])]
    public function delete(Request $request, Cakes $cake, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // ✅ Vérifie si l'utilisateur est admin
        if (!$user || !$user->isAdmin()) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        if ($this->isCsrfTokenValid('delete' . $cake->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cake);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cakes_index');
    }

    #[Route('/{id}/add-to-cart', name: 'app_cakes_add_to_cart', methods: ['POST'])]
    public function addToCart(int $id, Request $request, EntityManagerInterface $em, OrdersRepository $ordersRepo, LoggerInterface $logger): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $cake = $em->getRepository(\App\Entity\Cakes::class)->find($id);
        if (!$cake) {
            throw $this->createNotFoundException('Gâteau introuvable');
        }

        // Récupère ou crée le panier actif
        $cart = $ordersRepo->findOneBy(['client' => $user, 'is_paid' => false]);
        if (!$cart) {
            $cart = new \App\Entity\Orders();
            $cart->setClient($user);
            $cart->setIsPaid(false);
            $cart->setTotalPrice(0.0);
            $em->persist($cart);
        }

        $quantityToAdd = (int)$request->request->get('quantity', 1);

        $existingCakeOrder = $em->getRepository(\App\Entity\CakeOrder::class)
            ->findOneBy(['orders' => $cart, 'cake' => $cake]);

        if ($existingCakeOrder) {
            $existingCakeOrder->setQuantityCake($existingCakeOrder->getQuantityCake() + $quantityToAdd);
        } else {
            $cakeOrder = new \App\Entity\CakeOrder();
            $cakeOrder->setCake($cake);
            $cakeOrder->setQuantityCake($quantityToAdd);
            $cakeOrder->setOrders($cart);
            $em->persist($cakeOrder);
        }

        // Recalcul du total
        $total = 0.0;
        foreach ($cart->getCakeOrders() as $co) {
            $total += $co->getCake()->getPrice() * $co->getQuantityCake();
        }
        $cart->setTotalPrice($total);

        $em->flush();

        return $this->redirectToRoute('orders_show');
    }
}

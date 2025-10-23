<?php

namespace App\Controller;

use App\Entity\CakeOrder;
use App\Entity\Cakes;
use App\Entity\Orders;
use App\Entity\Clients;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Psr\Log\LoggerInterface;
use Throwable;

class OrdersController extends AbstractController
{
    

    #[Route('/orders', name: 'orders_show')]
    public function showOrders(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Clients) {
            return $this->redirectToRoute('app_login');
        }

        $order = $em->getRepository(Orders::class)
            ->findOneBy(['client' => $user, 'is_paid' => false]);

        return $this->render('orders/show.html.twig', [
            'cart' => $order?->getCakeOrders() ?? [],
            'order' => $order,
        ]);
    }
    #[Route('/orders/add/{id}/{quantity}', name: 'orders_add', requirements: ['quantity' => '\d+'], defaults: ['quantity' => 1])]
    public function addToOrders(
        int $id,
        int $quantity,
        Security $security,
        OrdersRepository $ordersRepo,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ): Response
    {
        $logger->info('addToOrders START', ['cake_id' => $id, 'quantity' => $quantity]);

        $user = $security->getUser();
        if (!$user instanceof Clients) {
            $logger->warning('Utilisateur invalide ou non connecté');
            throw $this->createAccessDeniedException();
        }
        $logger->debug('Utilisateur connecté', ['user_id' => $user->getId()]);

        try {
            // Récupérer le gâteau
            $logger->debug('Recherche du gâteau en base', ['cake_id' => $id]);
            $cake = $em->getRepository(Cakes::class)->find($id);
            if (!$cake) {
                $logger->error('Gâteau introuvable', ['cake_id' => $id]);
                throw $this->createNotFoundException();
            }
            $logger->debug('Gâteau trouvé', ['cake_id' => $cake->getId(), 'price' => $cake->getPrice()]);

            // Récupérer / créer le panier
            $logger->debug('Recherche du panier actif', ['user_id' => $user->getId()]);
            $cart = $ordersRepo->findOneBy(['client' => $user, 'is_paid' => false]);

            if ($cart) {
                $logger->debug('Panier existant trouvé', ['cart_id' => $cart->getId()]);
            } else {
                $logger->debug('Aucun panier trouvé — création d\'un nouveau panier', ['user_id' => $user->getId()]);
                $cart = new Orders();
                $cart->setClient($user);
                $cart->setIsPaid(false);
                $cart->setTotalPrice(0.0);
                $em->persist($cart);
                // flush plus tard
            }

            // État actuel du panier (liste des CakeOrders)
            $logger->debug('État avant modification — contenu du panier', [
                'cart_id' => $cart->getId() ?? null,
                'cakeOrders' => array_map(function($co){
                    return [
                        'id' => $co->getId(),
                        'cake_id' => $co->getCake()->getId(),
                        'quantity' => $co->getQuantityCake()
                    ];
                }, $cart->getCakeOrders()->toArray())
            ]);

            // Rechercher un CakeOrder existant (par cake id et cart)
            $existingCakeOrder = $em->getRepository(CakeOrder::class)->createQueryBuilder('co')
                ->where('co.orders = :cart')
                ->andWhere('co.cake = :cake')
                ->setParameters(['cart' => $cart, 'cake' => $cake])
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingCakeOrder) {
                $logger->info('CakeOrder existant trouvé — mise à jour quantité', [
                    'cakeOrder_id' => $existingCakeOrder->getId(),
                    'cake_id' => $cake->getId(),
                    'old_quantity' => $existingCakeOrder->getQuantityCake(),
                    'add_quantity' => $quantity
                ]);

                $existingCakeOrder->setQuantityCake($existingCakeOrder->getQuantityCake() + $quantity);

                $logger->debug('CakeOrder après update', [
                    'cakeOrder_id' => $existingCakeOrder->getId(),
                    'new_quantity' => $existingCakeOrder->getQuantityCake()
                ]);
            } else {
                $logger->info('Aucun CakeOrder existant — création d\'un nouveau', [
                    'cake_id' => $cake->getId(),
                    'add_quantity' => $quantity
                ]);

                $cakeOrder = new CakeOrder();
                $cakeOrder->setCake($cake);
                $cakeOrder->setQuantityCake($quantity);
                $cakeOrder->setOrders($cart);
                $em->persist($cakeOrder);

                $logger->debug('Nouveau CakeOrder persisté (pré-flush)', [
                    'cake_id' => $cake->getId(),
                    'quantity' => $quantity
                ]);
            }

            // Recalculer total
            $total = 0.0;
            foreach ($cart->getCakeOrders() as $co) {
                $total += $co->getCake()->getPrice() * $co->getQuantityCake();
            }
            $cart->setTotalPrice($total);
            $logger->debug('Total recalculé', ['cart_id' => $cart->getId() ?? null, 'total' => $total]);

            // Avant flush : log connection params (utile pour debug environnements)
            $connParams = $em->getConnection()->getParams();
            $logger->debug('Connection params', ['params' => $connParams]);

            // Sauvegarde
            $logger->info('Flush DB start');
            $em->flush();
            $logger->info('Flush DB OK');

            // État après flush
            $logger->debug('État après flush — contenu du panier', [
                'cart_id' => $cart->getId(),
                'cakeOrders' => array_map(function($co){
                    return [
                        'id' => $co->getId(),
                        'cake_id' => $co->getCake()->getId(),
                        'quantity' => $co->getQuantityCake()
                    ];
                }, $cart->getCakeOrders()->toArray())
            ]);

            $this->addFlash('success', sprintf('%d gâteau(x) ajouté(s) au panier', $quantity));
            $logger->info('addToOrders END', ['cart_id' => $cart->getId()]);

        } catch (Throwable $e) {
            $logger->error('Erreur durant addToOrders', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', "Erreur lors de l'ajout au panier");
            // Rethrow ou rediriger en fonction du comportement souhaité
            throw $e;
        }

        return $this->redirectToRoute('orders_show');
    }

    private function calculateTotal(Orders $cart): float
    {
        $total = 0;
        foreach ($cart->getCakeOrders() as $order) {
            $total += $order->getCake()->getPrice() * $order->getQuantityCake();
        }
        return $total;
    }

    private function calculateCartTotal(Orders $cart): float 
    {
        return array_reduce(
            $cart->getCakeOrders()->toArray(),
            function($total, $cakeOrder) {
                return $total + ($cakeOrder->getCake()->getPrice() * $cakeOrder->getQuantityCake());
            },
            0
        );
    }

    #[Route('/orders/update', name: 'orders_update', methods: ['POST'])]
    public function updateOrder(Request $request, Security $security, OrdersRepository $ordersRepo, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();
        if (!$user instanceof Clients) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $ordersRepo->findOneBy(['client' => $user, 'is_paid' => false]);
        if (!$cart) {
            return $this->redirectToRoute('orders_show');
        }

        foreach ($request->request->all() as $cakeOrderId => $quantity) {
            $cakeOrder = $em->getRepository(CakeOrder::class)->find($cakeOrderId);
            if ($cakeOrder && $cakeOrder->getOrders() === $cart) {
                $cakeOrder->setQuantityCake(max(1, (int)$quantity));
            }
        }

        $total = 0;
        foreach ($cart->getCakeOrders() as $co) {
            $total += $co->getQuantityCake() * $co->getCake()->getPrice();
        }
        $cart->setTotalPrice($total);

        $em->flush();
        $this->addFlash('success', 'Panier mis à jour !');

        return $this->redirectToRoute('orders_show');
    }

    #[Route('/orders/remove/{id}', name: 'orders_remove')]
    public function removeFromOrders(int $id, Security $security, OrdersRepository $ordersRepo, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();
        if (!$user instanceof Clients) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $ordersRepo->findOneBy(['client' => $user, 'is_paid' => false]);
        if (!$cart) {
            return $this->redirectToRoute('orders_show');
        }

        foreach ($cart->getCakeOrders() as $co) {
            if ($co->getCake()->getId() == $id) {
                $cart->removeCakeOrder($co);
                $em->remove($co);
                break;
            }
        }

        $total = 0;
        foreach ($cart->getCakeOrders() as $co) {
            $total += $co->getQuantityCake() * $co->getCake()->getPrice();
        }
        $cart->setTotalPrice($total);

        $em->flush();
        $this->addFlash('success', 'Produit supprimé !');

        return $this->redirectToRoute('orders_show');
    }

    #[Route('/orders/checkout', name: 'orders_checkout')]
    public function checkout(Security $security, OrdersRepository $ordersRepo, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();
        if (!$user instanceof Clients) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $ordersRepo->findOneBy(['client' => $user, 'is_paid' => false]);
        if (!$cart || $cart->getCakeOrders()->isEmpty()) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('orders_show');
        }

        $cart->setIsPaid(true);
        $em->flush();

        $this->addFlash('success', 'Commande validée avec succès !');
        return $this->redirectToRoute('app_cakes_index');
    }
}

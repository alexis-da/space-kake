<?php

namespace App\Controller;

use App\Entity\Cakes;
use App\Repository\CakesRepository;
use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class CakeController extends AbstractController
{
    #[Route('/cakes', name: 'list_cakes')]
    public function renderListCakePage(CakesRepository $cakesRepository) {
        $cakes = $cakesRepository->findAll();
        return $this->render("listCakes.html.twig", [
            'cakes' => $cakes
        ]);
    }

    #[Route('/cakes{id}', name: 'single_cake')]
    public function renderSingleCakePage($id, CakesRepository $cakesRepository){
        $cake = $cakesRepository->find($id);
        return $this->render("singleCake.html.twig", [
            'cake' => $cake
        ]);
    }

    #[Route('/create-cake')]
    public function renderCreateCakePage(Request $request, EntityManagerInterface $entityManager, CakesRepository $cakesRepository, ClientsRepository $clientsRepository){
        $title = null;
        $message = null;

        if ($request->isMethod('POST')) {
            $title =$request->request->get('title');
            $description =$request->request->get('description');
            $price =$request->request->get('price');
            $image =$request->request->get('image');

            $cake = new Cakes();
            $cake->setTitle($title);
            $cake->setDescription($description);
            $cake->setPrice((int)$price);
            $cake->setImage($image);

            $entityManager->persist($cake);
            $entityManager->flush();
            $message = "Gateaux bien crée.";
        }
        $user_id = $request->request->get('id');
        $user = $clientsRepository->find($user_id);
        return  $this->render("createCake.html.twig", [
            'title' => $title,
            'message' => $message,
            'user_id' => $user
        ]);
    }

    #[Route('/cakes/delete{id}', name: 'delete_product')]
    public function renderDeleteCake($id, Request $request, CakesRepository $cakesRepository, EntityManagerInterface $entityManager){
        $cake = $cakesRepository->find($id);

        $entityManager->remove($cake);
        $entityManager->flush();

        return $this->redirectToRoute('list-cakes');
    }

    public function renderUpdateCake($id, Request $request, EntityManagerInterface $entityManager, CakesRepository $cakesRepository){
        $title = null;
        $cake =$cakesRepository->find($id);

        if ($request->isMethod('POST')) {
            $title =$request->request->get('title');
            $description =$request->request->get('description');
            $price =$request->request->get('price');
            $image =$request->request->get('image');

            $cake = new Cakes();
            $cake->setTitle($title);
            $cake->setDescription($description);
            $cake->setPrice((int)$price);
            $cake->setImage($image);

            $entityManager->persist($cake);
            $entityManager->flush();
            return $this->redirectToRoute("list_cakes",);
        }
            return $this->render("updateCake.html.twig", [
                'cake' => $cake
            ]);

    }
}

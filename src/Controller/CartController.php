<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/panier', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository): Response
    {
        $panier = $session->get('panier', []);

        // On initialise des variables 
        $data = [];
        $total = 0;

        foreach($panier as $id => $quantity){
            $product = $productsRepository->find($id);

            $data[]= [
                'product' => $product, 
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('cart/index.html.twig', compact('data', 'total'));
    }

    #[Route('/ajouter/{id}', name: 'add')]
    public function add(Products $product, SessionInterface $session): Response
    {
        // On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        $panier = $session->get('panier', []);

        // On ajoute le produit dans le panier s'il n'y est pas encore
        // Sinon on incrémente sa quantité
        if(empty($panier[$id])){
            $panier[$id] = 1;
        }else{
            $panier[$id]++;
        }
        
        $session->set('panier', $panier);


        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->redirectToRoute('cart_index');

    }

    #[Route('/retirer/{id}', name: 'remove')]
    public function remove(Products $product, SessionInterface $session): Response
    {
        // On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        $panier = $session->get('panier', []);

        // On retire une quantité du produit dans le panier
        // Sinon on décrémente sa quantité
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }
        
        $session->set('panier', $panier);

        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->redirectToRoute('cart_index');

    }

    #[Route('/supprimer/{id}', name: 'delete')]
    public function delete(Products $product, SessionInterface $session): Response
    {
        // On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        $panier = $session->get('panier', []);

       
        if(!empty($panier[$id])){
                unset($panier[$id]);
        }
        
        $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index');

    }

    #[Route('/vider', name: 'empty')]
    public function empty(SessionInterface $session): Response
    {
        
        $session->remove('panier');

        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->redirectToRoute('cart_index');

    }
}

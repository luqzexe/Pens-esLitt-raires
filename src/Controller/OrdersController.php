<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commandes', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('orders/index.html.twig');
    }

    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em, UsersRepository $user): Response
    {
        $panier = $session->get('panier', []);

        // Afin de vérifier si le panier est vide lorsque l'on clique sur le bouton "Valider"
        // Ne me sert à rien car j'ai caché le bouton si le panier est vide
        if($panier === []){
            $this->addFlash('warning', 'Votre panier est vide !');
            $this->redirectToRoute('main');
        }

        // Panier non vide : on crée la commande
        $order = new Orders;

        // On rempli la commande
        $order->setUsers($this->getUser());
        $orderReference = 'REF-' . uniqid() . '-' . $order->getUsers()->getLastName();
        $order->setReference($orderReference);
        // $order->setReference(uniqid());

        // On parcourt le panier pour créer les détails de commande
        foreach($panier as $item => $quantity){
            $orderDetails = new OrdersDetails();

            // On va chercher le produit
            $product = $productsRepository->find($item);

            $price = $product->getPrice();

            $totalPrice = $price * $quantity;

            // On crée le détail de la commande
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($totalPrice);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
        }

        $em->persist($order);
        $em->flush();

        $session->remove('panier');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('main');
    }
}
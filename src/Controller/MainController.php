<?php

namespace App\Controller;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/', name: 'main')]
    public function index(): Response
    {
        // Récupération et randomisation des produits
        $productRepository = $this->em->getRepository(Products::class);
        $products = $productRepository->findAll();
        shuffle($products);

        $numberOfProducts = 5;
        $randomProducts = array_slice($products, 0, $numberOfProducts);

        // Récupérer les images associées aux produits aléatoires
        $imagesByProduct = [];
        foreach ($randomProducts as $product) {
            $images = $product->getImages();
            $imagesByProduct[$product->getId()] = $images;
        }

        return $this->render('main/index.html.twig', [
            'randomProducts' => $randomProducts,
            'imagesByProduct' => $imagesByProduct,
        ]);
    }
}


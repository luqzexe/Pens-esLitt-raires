<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('categories/index.html.twig', [
            'categories' => $categoriesRepository->findBy([],
            ['categoryOrder' => 'asc'])
        ]);
    }

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category): Response
    {
        // On va chercher la liste des produits de la catégories
        $products = $category->getProducts();

        return $this->render('categories/list.html.twig', compact('category', 'products'));
    }
}

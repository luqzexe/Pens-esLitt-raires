<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);

        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On crée un "nouveau produit"
        $category = new Categories();

        // On crée le formulaire
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);

        // On traite la requête du formulaire
        $categoryForm->handleRequest($request);

        // On vérifie si le formulaire est soumis ET valide
        if($categoryForm->isSubmitted() && $categoryForm->isValid()){
            // On génére le slug
            $slug = $slugger->slug($category->getName());
            $category->setSlug($slug);

            // On arrondit le prix
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke l'information
            $em->persist($category);
            $em->flush();

            $this->addFlash('success','Catégorie ajoutée avec succès');

            // On redirige 
            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/add.html.twig', [
            'categoryForm' => $categoryForm->createView()
        ]);
    }
    #[Route('/modifier/{id}', name: 'edit')]
    public function edit(Categories $category, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On divise le prix par 100
        // $prix = $product->getPrice() / 100;
        // $product->setPrice($prix);

        // On crée le formulaire
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);

        // On traite la requête du formulaire
        $categoryForm->handleRequest($request);

        // On vérifie si le formulaire est soumis ET valide
        if($categoryForm->isSubmitted() && $categoryForm->isValid()){

            // Générer le slug
            $slug = $slugger->slug($category->getName());
            $category->setSlug($slug);

            // On arrondit le prix
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke l'information
            $em->persist($category);
            $em->flush();

            $this->addFlash('success','Catégorie modifiée avec succès');

            // On redirige 
            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'categoryForm' => $categoryForm->createView(),
            'category' => $category
        ]);

        return $this->render('admin/categories/index.html.twig');
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Categories $category, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($category);
        $em->flush();
        
        $this->addFlash('success', 'Catégorie a été supprimé avec succès.');

        return $this->redirectToRoute('admin_categories_index');

    }
}
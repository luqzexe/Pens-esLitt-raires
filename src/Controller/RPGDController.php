<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RPGDController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'privacy_policy')]
    public function index(): Response
    {
        return $this->render('rpgd/index.html.twig');
    }
}

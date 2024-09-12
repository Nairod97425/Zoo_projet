<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'zoo_name' => 'Le Zoo Fantastique',
            'description' => 'Bienvenue au Zoo Fantastique, un endroit où les animaux de tous les continents cohabitent.',
        ]);
    }
}

<?php
// src/Controller/VeterinaireController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VeterinaireController extends AbstractController
{
    #[Route(path: '/veterinaire/dashboard', name: 'app_veterinaire_dashboard')]
    public function dashboard(): Response
    {
        // Logique pour le tableau de bord du vétérinaire
        return $this->render('veterinaire/dashboard.html.twig');
    }
}

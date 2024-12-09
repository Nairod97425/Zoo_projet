<?php
// src/Controller/EmployeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'app_employe_dashboard')]
    public function dashboard(): Response
    {
        // Logique pour le tableau de bord de l'employé
        return $this->render('employe/dashboard.html.twig');
    }
}


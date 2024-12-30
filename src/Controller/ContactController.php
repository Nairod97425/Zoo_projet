<?php

// src/Controller/ContactController.php
namespace App\Controller;

use App\Service\FirebaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContactController extends AbstractController
{

    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendMessage(Request $request): Response
    {
        // Exemple de token de l'appareil auquel vous voulez envoyer la notification
        $deviceToken = "DEVICE_TOKEN_HERE";
        $title = "Message reçu!";
        $body = "Merci de nous avoir contacté. Nous reviendrons vers vous bientôt.";

        // Envoi de la notification
        $result = $this->firebaseService->sendPushNotification($deviceToken, $title, $body);

        if ($result) {
            return new Response('Notification envoyée avec succès!');
        } else {
            return new Response('Échec de l\'envoi de la notification.', 500);
        }
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response
    {
        // Vérifier si l'utilisateur a le rôle nécessaire
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Redirection vers la page de connexion
        }

        // Créez un formulaire de contact
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Ajouter un message flash pour confirmer l'envoi
            $this->addFlash('success', 'Message envoyé avec succès !');

            // Redirection pour éviter une double soumission
            return $this->redirectToRoute('contact');
        }

        // Rendu de la page de contact avec le formulaire
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contact/send', name: 'contact_send', methods: ['POST'])]
    public function sendContact(Request $request): Response
    {
        // Vérifier si l'utilisateur a le rôle nécessaire
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès interdit.');
        }

        // Récupérer les données du formulaire envoyé
        $data = $request->request->get('contact');

        // Validation simple des données
        if (!isset($data['name'], $data['email'], $data['message'])) {
            $this->addFlash('error', 'Tous les champs sont requis.');
            return $this->redirectToRoute('contact');
        }

        // Ajout d'un message flash pour confirmer l'envoi
        $this->addFlash('success', 'Message envoyé avec succès !');

        // Redirection vers la page de contact
        return $this->redirectToRoute('contact');
    }
}

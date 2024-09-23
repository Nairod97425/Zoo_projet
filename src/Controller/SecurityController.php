<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class SecurityController extends AbstractController
{
    private $eventDispatcher;
    private $logger;
    private $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // // Check if the user has submitted the login form
        // if ($this->isGranted('ROLE_ADMIN')) {
        //     // User is authenticated and has the ROLE_ADMIN role, redirect to the admin page
        //     return $this->redirectToRoute('admin_utilisateurs');
        // }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(Request $request): RedirectResponse
    {
        // Déconnexion de l'utilisateur
        $token = $this->tokenStorage->getToken();
        if ($token) {
            $logoutEvent = new LogoutEvent($request, $token);
            $this->eventDispatcher->dispatch($logoutEvent, LogoutEvent::class);

            $user = $token->getUser();
            if ($user) {
                $this->logger->info('User has been logged out.', ['user' => $user->getUserIdentifier()]);
            } else {
                $this->logger->info('Attempted logout for anonymous user.');
            }
        } else {
            $this->logger->info('No user token found for logout.');
        }

        // Redirection vers la page de connexion
        return $this->redirectToRoute('home');
    }

}
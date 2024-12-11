<?php
// src/Security/AuthAuthenticator.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AuthAuthenticator extends AbstractAuthenticator
{
    private $urlGenerator;
    private $csrfTokenManager;
    private $requestStack;
    private $entityManager;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_email');
        $plainPassword = $request->request->get('_password');

        if (!$email || !$plainPassword) {
            throw new AuthenticationException('Email ou mot de passe manquant.');
        }

        $csrfToken = $request->request->get('_csrf_token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            throw new AuthenticationException('Invalid CSRF token.');
        }

        // Recherche de l'utilisateur
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user || !password_verify($plainPassword, $user->getPassword())) {
            throw new AuthenticationException('Identifiants incorrects.');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($plainPassword),
            [new CsrfTokenBadge('authenticate', $csrfToken)]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $roleRedirects = [
            'ROLE_ADMIN' => 'app_admin_dashboard',
            'ROLE_EMPLOYE' => 'app_employe_dashboard',
            'ROLE_VETERINAIRE' => 'app_veterinaire_dashboard',
        ];

        foreach ($roleRedirects as $role => $route) {
            if (in_array($role, $user->getRoles(), true)) {
                return new RedirectResponse($this->urlGenerator->generate($route));
            }
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $session = $this->requestStack->getSession();

        if ($session) {
            $session->set('message', 'E-mail ou mot de passe incorrect.');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}

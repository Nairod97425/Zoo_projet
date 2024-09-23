<?php

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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class AuthAuthenticator extends AbstractAuthenticator
{
    private $urlGenerator;
    private $requestStack;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('Email');
        $password = $request->request->get('Mot_de_passe');

        if (empty($email)) {
            throw new \InvalidArgumentException('L\'email ne peut pas être vide.');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        if (in_array('ROLE_EMPLOYER', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('employe_dashboard'));
        }

        if (in_array('ROLE_VETERINAIRE', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('veterinaire_dashboard'));
        }

        // Redirection par défaut
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $session = $this->requestStack->getSession();
        if (!$session->has('flash')) {
            $session->set('flash', new FlashBag());
        }
        $flashBag = $session->get('flash');
        $flashBag->add('error', 'E-mail ou mot de passe incorrect.', true);
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}

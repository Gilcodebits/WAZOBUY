<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    private UrlGeneratorInterface $router;
    private TokenStorageInterface $tokenStorage;

    public function __construct(UrlGeneratorInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return new RedirectResponse($this->router->generate('app_login'));
        }

        // Récupérer les rôles de l'utilisateur
        $roles = $user->getRoles();

        // Redirection basée sur les rôles
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('app_dashboard'));
        }

        if (in_array('ROLE_VENDEUR', $roles)) {
            return new RedirectResponse($this->router->generate('app_boutique'));
        }

        if (in_array('ROLE_LIVREUR', $roles)) {
            return new RedirectResponse($this->router->generate('app_deliver_dashboard'));
        }

        // Ajout pour les clients
        if (in_array('ROLE_CLIENT', $roles)) {
            return new RedirectResponse($this->router->generate('app_client_dashboard'));
        }

        if (in_array('ROLE_USER', $roles)) {
            return new RedirectResponse($this->router->generate('app_home'));
        }

        // Si aucun rôle ne correspond, rediriger vers l'accueil
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // Stocker l'erreur dans la session pour l'afficher
        $session = $request->getSession();
        if ($session) {
            $session->set('_security.last_error', $exception->getMessage());
        }
        
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
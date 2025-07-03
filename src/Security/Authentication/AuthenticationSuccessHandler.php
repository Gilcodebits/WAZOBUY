<?php

namespace App\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    private $router;

    public function __construct(
        HttpUtils $httpUtils,
        RouterInterface $router,
        array $options = []
    ) {
        parent::__construct($httpUtils, $options);
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();
        
        if ($user instanceof UserInterface) {
            // Récupérer le rôle de l'utilisateur
            $roles = $user->getRoles();
            
            // Le rôle est stocké sous la forme 'ROLE_CLIENT', 'ROLE_ADMIN', etc.
            if (in_array('ROLE_CLIENT', $roles)) {
                return new Response(null, 302, [
                    'Location' => $this->router->generate('client_home')
                ]);
            }

            elseif (in_array('ROLE_ADMIN', $roles)) {
               return new Response(null, 302, [
                   'Location' => $this->router->generate('app_admin_dashboard')
                ]);
            }
        }
        
        return parent::onAuthenticationSuccess($request, $token);
    }
}

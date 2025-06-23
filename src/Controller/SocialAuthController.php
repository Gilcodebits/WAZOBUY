<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SocialAuthController extends AbstractController
{
    /**
     * @Route("/login/check-google", name="connect_check_google")
     */
    public function checkGoogle(): Response
    {
        throw new \Exception('Should never be reached!');
    }

    /**
     * @Route("/login/check-facebook", name="connect_check_facebook")
     */
    public function checkFacebook(): Response
    {
        throw new \Exception('Should never be reached!');
    }
}
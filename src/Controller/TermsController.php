<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TermsController extends AbstractController
{
    #[Route('/conditions-utilisation', name: 'app_terms')]
    public function terms()
    {
        return $this->render('terms/terms.html.twig');
    }

    #[Route('/politique-confidentialite', name: 'app_privacy_policy')]
    public function privacyPolicy()
    {
        return $this->render('terms/privacy_policy.html.twig');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SellerController extends AbstractController
{
    #[Route('/devenir-vendeur', name: 'app_become_seller')]
    public function becomeSeller(): Response
    {
        return $this->render('seller/register.html.twig');
    }
}
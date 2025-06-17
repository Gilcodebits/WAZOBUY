<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryController extends AbstractController
{
    #[Route('/livreur/dashboard', name: 'app_delivery_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('delivery/dashboard.html.twig');
    }
}
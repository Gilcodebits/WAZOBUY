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

    #[Route('/livreur/profile', name: 'app_delivery_profile')]
    public function profile(): Response
    {
        return $this->render('delivery/profile.html.twig');
    }

    #[Route('/livreur/orders', name: 'app_delivery_orders')]
    public function orders(): Response
    {
        return $this->render('delivery/orders.html.twig');
    }
}
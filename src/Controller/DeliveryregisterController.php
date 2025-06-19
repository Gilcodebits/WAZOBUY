<?php
// src/Controller/DeliveyregisterController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryregisterController extends AbstractController
{
    #[Route('/devenir-livreur', name: 'app_deliveryregister')]
    public function index(): Response
    {
        return $this->render('deliverydashbord/deliveryregister.html.twig');
    }
}
?>
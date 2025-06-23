<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ClientOrdersController extends AbstractController
{
    #[Route('/client/orders', name: 'app_client_orders')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $commandes = $commandeRepository->findBy(['client' => $user], ['createdAt' => 'DESC']);

        return $this->render('clientdashbord/orders.html.twig', [
            'commandes' => $commandes
        ]);
    }
}

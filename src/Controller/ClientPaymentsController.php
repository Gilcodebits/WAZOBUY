<?php

namespace App\Controller;

use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ClientPaymentsController extends AbstractController
{
    #[Route('/client/payments', name: 'app_client_payments')]
    public function index(PaiementRepository $paiementRepository): Response
    {
        $user = $this->getUser();
        $paiements = $paiementRepository->findBy(['client' => $user], ['datePaiement' => 'DESC']);

        return $this->render('clientdashbord/payments.html.twig', [
            'paiements' => $paiements
        ]);
    }
}

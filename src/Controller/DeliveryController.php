<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class DeliveryController extends AbstractController
{
    #[Route('/livreur/dashboard', name: 'app_deliver_dashboard')]
    public function dashboard(): Response
    {
        // Simuler des données de commandes
        $orders = [
            [
                'id' => 1,
                'location' => '123 Rue du Commerce, Cotonou',
                'product' => '3 articles variés',
                'deliveryTime' => new \DateTime('now'),
                'customer_name' => 'Jean Dupont',
                'address' => '123 Rue du Commerce, Cotonou',
                'total' => '15.000 FCFA',
                'items' => 3,
                'status' => 'pending'
            ],
            [
                'id' => 2,
                'location' => '456 Avenue de la Paix, Porto-Novo',
                'product' => '2 articles variés',
                'deliveryTime' => new \DateTime('now'),
                'customer_name' => 'Marie Martin',
                'address' => '456 Avenue de la Paix, Porto-Novo',
                'total' => '20.000 FCFA',
                'items' => 2,
                'status' => 'pending'
            ]
        ];

        // Simuler les données de l'utilisateur
        $user = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'livreur@wazobuy.com',
            'rating' => 4.5,
            'total_deliveries' => 125,
            'current_status' => 'En ligne'
        ];

        // Simuler les statistiques
        $stats = [
            'total_deliveries' => 125,
            'completed_deliveries' => 118,
            'pending_deliveries' => 7,
            'average_rating' => 4.5,
            'total_earnings' => '250.000 FCFA',
            'deliveries_today' => 15,
            'satisfaction_rate' => 92.5,
            'distance_couverte'=>'100km',
            'weekly_deliveries' => [
                ['day' => 'Lundi', 'count' => 15],
                ['day' => 'Mardi', 'count' => 18],
                ['day' => 'Mercredi', 'count' => 12],
                ['day' => 'Jeudi', 'count' => 16],
                ['day' => 'Vendredi', 'count' => 19],
                ['day' => 'Samedi', 'count' => 15],
                ['day' => 'Dimanche', 'count' => 8]
            ]
        ];

        return $this->render('deliverydashbord/deliverdashboard.html.twig', [
            'orders' => $orders,
            'user' => $user,
            'stats' => $stats
        ]);
    }

    #[Route('/livreur/accept/{orderId}', name: 'accept_order')]
    public function acceptOrder(int $orderId): Response
    {
        // Logique pour accepter la commande
        // Ici, nous devrions mettre à jour la base de données
        
        // Pour l'instant, on redirige vers le tableau de bord
        return $this->redirectToRoute('app_deliver_dashboard');
    }
}
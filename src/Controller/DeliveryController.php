<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Utilisateur;

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

        // Récupérer les données de l'utilisateur connecté
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur est un livreur
        if (!$user || !$user instanceof Utilisateur || $user->getRole() !== 'livreur') {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }

        // Simuler les statistiques
        $stats = array(
            'total_deliveries' => 125,
            'completed_deliveries' => 118,
            'pending_deliveries' => 7,
            'average_rating' => 4.5,
            'total_earnings' => '250.000 FCFA',
            'deliveries_today' => 15,
            'satisfaction_rate' => 92.5,
            'distance_couverte' => '100km',
            'weekly_deliveries' => array(
                array('day' => 'Lundi', 'count' => 15),
                array('day' => 'Mardi', 'count' => 18),
                array('day' => 'Mercredi', 'count' => 12),
                array('day' => 'Jeudi', 'count' => 16),
                array('day' => 'Vendredi', 'count' => 19),
                array('day' => 'Samedi', 'count' => 15),
                array('day' => 'Dimanche', 'count' => 8)
            )
        );

        // Extraire les informations de l'utilisateur
        $userInfo = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'estActif' => $user->isEstActif()
        ];

        return $this->render('deliverydashbord/deliverdashboard.html.twig', [
            'orders' => $orders,
            'userInfo' => $userInfo,
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
?>
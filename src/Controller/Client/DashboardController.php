<?php

namespace App\Controller\Client;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // Récupérer les commandes de l'utilisateur
        $commandes = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->where('c.client = :client')
            ->setParameter('client', $user)
            ->orderBy('c.dateCommande', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Récupérer les produits favoris de l'utilisateur
        $produitsFavoris = $entityManager
            ->getRepository(Produit::class)
            ->createQueryBuilder('p')
            ->where('p.favoris LIKE :email')
            ->setParameter('email', '%' . $user->getEmail() . '%')
            ->orderBy('p.dateAjout', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('client/dashboard/index.html.twig', [
            'commandesRecentes' => $commandes,
            'produitsFavoris' => $produitsFavoris
        ]);
    }
}

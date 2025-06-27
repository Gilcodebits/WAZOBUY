<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\Paiement;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\PaiementRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTimeImmutable;

#[Route('/vendeur')]
#[IsGranted('ROLE_VENDEUR')]
class SellerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_seller_dashboard')]
    public function dashboard(
        CommandeRepository $commandeRepository,
        ProduitRepository $produitRepository,
        PaiementRepository $paiementRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        $seller = $this->getUser();
        
        // Statistiques des ventes
        $totalVentes = $commandeRepository->createQueryBuilder('c')
            ->select('SUM(c.montantTotal)')
            ->where('c.vendeur = :seller')
            ->andWhere('c.statut = :statut')
            ->setParameter('seller', $seller)
            ->setParameter('statut', 'livree')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $commandesMois = $commandeRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.vendeur = :seller')
            ->andWhere('c.dateCommande >= :debutMois')
            ->setParameter('seller', $seller)
            ->setParameter('debutMois', new DateTimeImmutable('first day of this month'))
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $produitsActifs = $produitRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.vendeur = :seller')
            ->andWhere('p.stock > 0')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $revenusMois = $paiementRepository->createQueryBuilder('p')
            ->select('SUM(p.montant)')
            ->where('p.client = :seller')
            ->andWhere('p.datePaiement >= :debutMois')
            ->andWhere('p.statut = :statut')
            ->setParameter('seller', $seller)
            ->setParameter('debutMois', new DateTimeImmutable('first day of this month'))
            ->setParameter('statut', 'complete')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Commandes récentes
        $commandesRecentes = $commandeRepository->createQueryBuilder('c')
            ->where('c.vendeur = :seller')
            ->orderBy('c.dateCommande', 'DESC')
            ->setMaxResults(5)
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult();

        // Produits populaires (simplifié)
        $produitsPopulaires = $produitRepository->createQueryBuilder('p')
            ->where('p.vendeur = :seller')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult();

        // Statistiques des 7 derniers jours
        $stats7Jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = new DateTimeImmutable("-$i days");
            $debutJour = $date->setTime(0, 0, 0);
            $finJour = $date->setTime(23, 59, 59);

            $ventesJour = $commandeRepository->createQueryBuilder('c')
                ->select('SUM(c.montantTotal)')
                ->where('c.vendeur = :seller')
                ->andWhere('c.dateCommande >= :debut')
                ->andWhere('c.dateCommande <= :fin')
                ->andWhere('c.statut = :statut')
                ->setParameter('seller', $seller)
                ->setParameter('debut', $debutJour)
                ->setParameter('fin', $finJour)
                ->setParameter('statut', 'livree')
                ->getQuery()
                ->getSingleScalarResult() ?? 0;

            $stats7Jours[] = [
                'date' => $date->format('d/m'),
                'ventes' => $ventesJour
            ];
        }

        return $this->render('seller/sellerdashbord.html.twig', [
            'totalVentes' => $totalVentes,
            'commandesMois' => $commandesMois,
            'produitsActifs' => $produitsActifs,
            'revenusMois' => $revenusMois,
            'commandesRecentes' => $commandesRecentes,
            'produitsPopulaires' => $produitsPopulaires,
            'stats7Jours' => $stats7Jours
        ]);
    }

    #[Route('/commandes', name: 'app_seller_orders')]
    public function orders(CommandeRepository $commandeRepository): Response
    {
        $seller = $this->getUser();
        
        $commandes = $commandeRepository->createQueryBuilder('c')
            ->where('c.vendeur = :seller')
            ->orderBy('c.dateCommande', 'DESC')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult();

        return $this->render('seller/orders.html.twig', [
            'commandes' => $commandes
        ]);
    }

    #[Route('/produits', name: 'app_seller_products')]
    public function products(ProduitRepository $produitRepository): Response
    {
        $seller = $this->getUser();
        
        $produits = $produitRepository->createQueryBuilder('p')
            ->where('p.vendeur = :seller')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult();

        return $this->render('seller/products.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/paiements', name: 'app_seller_payments')]
    public function payments(PaiementRepository $paiementRepository): Response
    {
        $seller = $this->getUser();
        
        $paiements = $paiementRepository->createQueryBuilder('p')
            ->where('p.client = :seller')
            ->orderBy('p.datePaiement', 'DESC')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult();

        return $this->render('seller/payments.html.twig', [
            'paiements' => $paiements
        ]);
    }

    #[Route('/rapports', name: 'app_seller_reports')]
    public function reports(
        CommandeRepository $commandeRepository,
        ProduitRepository $produitRepository,
        PaiementRepository $paiementRepository
    ): Response {
        $seller = $this->getUser();
        
        // Statistiques annuelles
        $annee = date('Y');
        $debutAnnee = new DateTimeImmutable("$annee-01-01");
        $finAnnee = new DateTimeImmutable("$annee-12-31");

        $ventesAnnuelles = $commandeRepository->createQueryBuilder('c')
            ->select('SUM(c.montantTotal)')
            ->where('c.vendeur = :seller')
            ->andWhere('c.dateCommande >= :debut')
            ->andWhere('c.dateCommande <= :fin')
            ->andWhere('c.statut = :statut')
            ->setParameter('seller', $seller)
            ->setParameter('debut', $debutAnnee)
            ->setParameter('fin', $finAnnee)
            ->setParameter('statut', 'livree')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $commandesAnnuelles = $commandeRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.vendeur = :seller')
            ->andWhere('c.dateCommande >= :debut')
            ->andWhere('c.dateCommande <= :fin')
            ->setParameter('seller', $seller)
            ->setParameter('debut', $debutAnnee)
            ->setParameter('fin', $finAnnee)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $produitsVendus = $produitRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.vendeur = :seller')
            ->andWhere('p.createdAt >= :debut')
            ->andWhere('p.createdAt <= :fin')
            ->setParameter('seller', $seller)
            ->setParameter('debut', $debutAnnee)
            ->setParameter('fin', $finAnnee)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        return $this->render('seller/reports.html.twig', [
            'ventesAnnuelles' => $ventesAnnuelles,
            'commandesAnnuelles' => $commandesAnnuelles,
            'produitsVendus' => $produitsVendus,
            'annee' => $annee
        ]);
    }

    #[Route('/parametres', name: 'app_seller_settings')]
    public function settings(): Response
    {
        return $this->render('seller/settings.html.twig');
    }

    // API pour les notifications
    #[Route('/api/notifications', name: 'app_seller_api_notifications', methods: ['GET'])]
    public function getNotifications(): JsonResponse
    {
        // Simuler des notifications réelles
        $notifications = [
            [
                'id' => 1,
                'titre' => 'Nouvelle commande',
                'message' => 'Vous avez reçu une nouvelle commande #1234',
                'date' => new \DateTime('-2 hours'),
                'lu' => false
            ],
            [
                'id' => 2,
                'titre' => 'Stock faible',
                'message' => 'Le produit "iPhone 14 Pro" est en rupture de stock',
                'date' => new \DateTime('-1 day'),
                'lu' => true
            ]
        ];

        return new JsonResponse($notifications);
    }

    // API pour les messages
    #[Route('/api/messages', name: 'app_seller_api_messages', methods: ['GET'])]
    public function getMessages(): JsonResponse
    {
        // Simuler des messages réels
        $messages = [
            [
                'id' => 1,
                'expediteur' => 'Support Client',
                'sujet' => 'Question sur votre produit',
                'message' => 'Un client a une question sur votre produit',
                'date' => new \DateTime('-1 hour'),
                'lu' => false
            ]
        ];

        return new JsonResponse($messages);
    }
}
<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommandeRepository;

class ProduitController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function dashboard(Request $request, ProduitRepository $produitRepository, CommandeRepository $commandeRepository): Response
    {
        // Récupération des filtres depuis la requête
        $filters = [
            'categorie' => $request->query->get('categorie', null),
            'prix_min' => $request->query->get('prix_min', null),
            'prix_max' => $request->query->get('prix_max', null),
            'note_min' => $request->query->get('note_min', null),
            'tri' => $request->query->get('tri', 'popularite'),
            'page' => $request->query->get('page', 1),
            'search' => $request->query->get('search', ''),
        ];

        // Récupération des produits avec pagination
        $produits = $produitRepository->findWithFilters($filters, 6); // 6 produits par page
        $totalProduits = $produitRepository->count([]);
        $totalPages = ceil($totalProduits / 6);

        // Récupération du panier depuis la session
        $panier = $this->getPanier($request);
        $panierCount = count($panier);

        $user = $this->getUser();
        $commandesRecentes = [];
        if ($user) {
            $commandesRecentes = $commandeRepository->findRecentByClient($user, 5);
        }

        $produitsFavoris = [];

        return $this->render('client/dashboard/index.html.twig', [
            'produits' => $produits,
            'filters' => $filters,
            'totalProduits' => $totalProduits,
            'totalPages' => $totalPages,
            'panierCount' => $panierCount,
            'commandesRecentes' => $commandesRecentes,
            'produitsFavoris' => $produitsFavoris
        ]);
    }

    #[Route('/produits/{id}', name: 'app_produit_show')]
    public function show(Produit $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit
        ]);
    }

    #[Route('/panier', name: 'app_panier')]
    #[Route('/panier', name: 'app_panier')]
    public function panier(Request $request): Response
    {
        // Récupération du panier depuis la session
        $panier = $this->getPanier($request);
        
        return $this->render('panier/index.html.twig', [
            'panier' => $panier
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter')]
    public function ajouterAuPanier(Produit $produit, Request $request): Response
    {
        // Récupération du panier depuis la session
        $panier = $this->getPanier($request);
        
        // Ajout du produit au panier
        $panierId = $produit->getId();
        if (!isset($panier[$panierId])) {
            $panier[$panierId] = [
                'produit' => $produit,
                'quantite' => 1
            ];
        } else {
            $panier[$panierId]['quantite']++;
        }
        
        // Sauvegarde du panier dans la session
        $request->getSession()->set('panier', $panier);
        
        $this->addFlash('success', 'Produit ajouté au panier avec succès');
        return $this->redirectToRoute('app_produits');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimerDuPanier(Produit $produit, Request $request): Response
    {
        $panier = $this->getPanier($request);
        $panierId = $produit->getId();
        
        if (isset($panier[$panierId])) {
            unset($panier[$panierId]);
            $request->getSession()->set('panier', $panier);
            $this->addFlash('success', 'Produit supprimé du panier avec succès');
        }
        
        return $this->redirectToRoute('app_panier');
    }

    private function getPanier(Request $request): array
    {
        return $request->getSession()->get('panier', []);
    }
}

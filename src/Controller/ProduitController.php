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
    #[Route('/produits/{id}', name: 'app_produit_show')]
    public function show(Produit $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit
        ]);
    }

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

    #[Route('/panier/count', name: 'app_panier_count', methods: ['GET'])]
    public function countPanier(Request $request): Response
    {
        $panier = $this->getPanier($request);
        $count = 0;
        foreach ($panier as $item) {
            $count += $item['quantite'] ?? 1;
        }
        return $this->json(['count' => $count]);
    }

    private function getPanier(Request $request): array
    {
        return $request->getSession()->get('panier', []);
    }
}

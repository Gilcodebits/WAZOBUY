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
        
        $count = 0;
        foreach ($panier as $item) {
            $count += $item['quantite'] ?? 1;
        }
        
        return $this->json(['success' => true, 'count' => $count]);
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

    #[Route('/panier/liste', name: 'app_panier_liste', methods: ['GET'])]
    public function listePanier(Request $request): Response
    {
        $panier = $this->getPanier($request);
        $items = [];
        $total = 0;
        
        foreach ($panier as $produitId => $item) {
            $produit = $item['produit'];
            $quantite = $item['quantite'];
            $prix = $produit->getPrix();
            
            $items[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $prix,
                'quantite' => $quantite,
                'image' => $produit->getImage(),
                'description' => $produit->getDescription()
            ];
            
            $total += $prix * $quantite;
        }
        
        return $this->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    #[Route('/panier/update/{id}', name: 'app_panier_update', methods: ['POST'])]
    public function updatePanier(Produit $produit, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $nouvelleQuantite = $data['quantite'] ?? 1;
        
        $panier = $this->getPanier($request);
        $panierId = $produit->getId();
        
        if ($nouvelleQuantite <= 0) {
            unset($panier[$panierId]);
        } else {
            $panier[$panierId]['quantite'] = $nouvelleQuantite;
        }
        
        $request->getSession()->set('panier', $panier);
        
        $count = 0;
        foreach ($panier as $item) {
            $count += $item['quantite'] ?? 1;
        }
        
        return $this->json(['success' => true, 'count' => $count]);
    }

    #[Route('/panier/remove/{id}', name: 'app_panier_remove', methods: ['POST'])]
    public function removeFromPanier(Produit $produit, Request $request): Response
    {
        $panier = $this->getPanier($request);
        $panierId = $produit->getId();
        
        if (isset($panier[$panierId])) {
            unset($panier[$panierId]);
            $request->getSession()->set('panier', $panier);
        }
        
        $count = 0;
        foreach ($panier as $item) {
            $count += $item['quantite'] ?? 1;
        }
        
        return $this->json(['success' => true, 'count' => $count]);
    }

    #[Route('/api/user/check', name: 'api_user_check', methods: ['GET'])]
    public function checkUser(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['authenticated' => false], 401);
        }
        return $this->json(['authenticated' => true]);
    }

    #[Route('/api/user/info', name: 'api_user_info', methods: ['GET'])]
    public function getUserInfo(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Non connecté'], 401);
        }
        
        return $this->json([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'photoProfil' => $user->getPhotoProfil()
        ]);
    }

    private function getPanier(Request $request): array
    {
        return $request->getSession()->get('panier', []);
    }
}

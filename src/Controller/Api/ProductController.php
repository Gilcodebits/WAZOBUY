<?php
namespace App\Controller\Api;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/product/{id}', name: 'api_product_show', methods: ['GET'])]
    public function show($id, ProduitRepository $repo): JsonResponse
    {
        $produit = $repo->find($id);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }
        return $this->json([
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix' => number_format($produit->getPrix(), 0, '.', ' ') . ' FCFA',
            'description' => $produit->getDescription(),
            'image' => $produit->getImage() ? '/uploads/products/' . $produit->getImage() : null,
            'vendeur' => $produit->getVendeur()->getNom(),
            'condition' => $produit->getCondition(),
        ]);
    }
} 
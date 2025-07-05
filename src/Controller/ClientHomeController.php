<?php
namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientHomeController extends AbstractController
{
    #[Route('/client', name: 'client_home')]
    public function index(ProduitRepository $produitRepo, CategorieRepository $categorieRepo): Response
    {
        $categories = $categorieRepo->findAll();
        $categoriesWithProducts = [];
        foreach ($categories as $categorie) {
            $produits = $produitRepo->findBy(
                ['categorie' => $categorie],
                ['id' => 'DESC'],
                4 // Limite à 4 produits par catégorie
            );
            $categoriesWithProducts[] = [
                'categorie' => $categorie,
                'produits' => $produits
            ];
        }
        $user = $this->getUser();
        return $this->render('client/home.html.twig', [
            'categoriesWithProducts' => $categoriesWithProducts,
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    #[Route('/client/product/{id}', name: 'client_product_detail')]
    public function productDetail(int $id, ProduitRepository $produitRepo): Response
    {
        $produit = $produitRepo->find($id);
        
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
        return $this->render('client/product_detail.html.twig', [
            'produit' => $produit,
        ]);
    }
} 
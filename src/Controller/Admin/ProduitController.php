<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/admin/produits', name: 'app_admin_produits')]
    public function index(ProduitRepository $produitRepository): Response
    {
        // Récupérer tous les produits
        $produits = $produitRepository
            ->createQueryBuilder('p')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/produits/index.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/admin/produits/{id}', name: 'app_admin_produit_show', requirements: ['id' => '\d+'])]
    public function show(Produit $produit): Response
    {
        return $this->render('admin/produits/show.html.twig', [
            'produit' => $produit
        ]);
    }

    #[Route('/admin/produits/{id}/activer', name: 'app_admin_produit_activer', requirements: ['id' => '\d+'])]
    public function activer(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $produit->setPromotion(true);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit est maintenant en promotion.');
        return $this->redirectToRoute('app_admin_produits');
    }

    #[Route('/admin/produits/{id}/desactiver', name: 'app_admin_produit_desactiver', requirements: ['id' => '\d+'])]
    public function desactiver(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $produit->setPromotion(false);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit n\'est plus en promotion.');
        return $this->redirectToRoute('app_admin_produits');
    }
}

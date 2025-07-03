<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

// #[Route('/client/dashboard', name: 'app_client_dashboard')]
class ClientDashboardController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        // Debug pour vérifier que le contrôleur est appelé
        dump('ClientDashboardController appelé');
        dump($request->getPathInfo());
        // Récupérer toutes les catégories avec le nombre de produits en une seule requête
        $categories = $entityManager
            ->getRepository(Categorie::class)
            ->createQueryBuilder('c')
            ->leftJoin('c.produits', 'p')
            ->addSelect('COUNT(p.id) as nbProduits')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        // Formater les catégories
        $categoriesWithCounts = array_map(function($category) {
            return [
                'id' => $category->getId(),
                'nom' => $category->getNom(),
                'nbProduits' => $category->getNbProduits() ?? 0
            ];
        }, $categories);

        // Debug - Vérifier les catégories avec comptage
        dump($categoriesWithCounts);

        // Debug - Vérifier le résultat de la requête
        dump($categories);

        // Debug pour vérifier la structure des catégories
        dump($categories);

        // Les catégories sont déjà formatées comme souhaité
        $formattedCategories = $categoriesWithCounts;

        // Récupérer tous les produits avec pagination et relations nécessaires
        $query = $entityManager
            ->getRepository(Produit::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.vendeur', 'v')
            ->addSelect('v')
            ->getQuery();

        $produits = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12 // Nombre de produits par page
        );

        // Le total est déjà calculé par la pagination
        $totalProduits = $produits->getTotalItemCount();

        // Récupérer les notes des produits
        $notes = $entityManager
            ->getRepository(Produit::class)
            ->createQueryBuilder('p')
            ->select('p.note, COUNT(p.id) as count')
            ->groupBy('p.note')
            ->getQuery()
            ->getResult();

        // Transformer les notes en tableau associatif
        $notesArray = [];
        foreach ($notes as $note) {
            $notesArray[$note['note']] = $note['count'];
        }

        // Récupérer le paramètre de tri
        $sort = $request->query->get('sort', 'popularity');

        // Récupérer les filtres de prix
        $prix = $request->query->get('prix', []);



        // Debug - Vérifier la valeur finale de formattedCategories
        dump($formattedCategories);

        // Assurer que les variables sont définies
        $categories = $formattedCategories ?? [];
        $produits = $produits ?? [];
        $totalProduits = $totalProduits ?? 0;
        $notes = $notesArray ?? [];
        $sort = $sort ?? 'popularity';
        $prix = $prix ?? [];

        return $this->render('client/home.html.twig', [
            'categories' => $categories,
            'produits' => $produits,
            'totalProduits' => $totalProduits,
            'notes' => $notes,
            'sort' => $sort,
            'prix' => $prix
        ]);
    }
}

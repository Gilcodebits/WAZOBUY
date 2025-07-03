<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findAllOrderByPopularity(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.note', 'DESC')
            ->addOrderBy('p.nombreAvis', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findWithFilters(array $filters, ?int $limit = null, int $page = 1): array
    {
        $query = $this->createQueryBuilder('p');

        // Filtre par catégorie
        if (isset($filters['categorie']) && $filters['categorie'] !== 'tous') {
            $query->andWhere('p.categorie = :categorie')
                ->setParameter('categorie', $filters['categorie']);
        }

        // Filtre par prix
        if (isset($filters['prix_min'])) {
            $query->andWhere('p.prix >= :prix_min')
                ->setParameter('prix_min', $filters['prix_min']);
        }

        if (isset($filters['prix_max'])) {
            $query->andWhere('p.prix <= :prix_max')
                ->setParameter('prix_max', $filters['prix_max']);
        }

        // Filtre par note
        if (isset($filters['note_min'])) {
            $query->andWhere('p.note >= :note_min')
                ->setParameter('note_min', $filters['note_min']);
        }

        // Tri des produits
        $tri = $filters['tri'] ?? 'popularite';
        switch ($tri) {
            case 'prix_asc':
                $query->orderBy('p.prix', 'ASC');
                break;
            case 'prix_desc':
                $query->orderBy('p.prix', 'DESC');
                break;
            case 'nouveautes':
                $query->orderBy('p.dateAjout', 'DESC');
                break;
            default:
                $query->orderBy('p.note', 'DESC')
                    ->addOrderBy('p.nombreAvis', 'DESC');
        }

        // Pagination
        if ($limit) {
            $offset = ($page - 1) * $limit;
            $query->setFirstResult($offset)
                  ->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Retourne une Query pour les produits filtrés (catégorie, prix, note, recherche, tri)
     */
    public function findPublishedWithFiltersQuery($categorie = null, $prix = [], $note = null, $search = null, $sort = 'popularity')
    {
        $qb = $this->createQueryBuilder('p');

        // Filtre catégorie (string)
        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }

        // Recherche
        if ($search) {
            $qb->andWhere('LOWER(p.nom) LIKE :search OR LOWER(p.description) LIKE :search')
               ->setParameter('search', '%' . strtolower($search) . '%');
        }

        // Filtres prix
        if ($prix && is_array($prix)) {
            $orX = $qb->expr()->orX();
            foreach ($prix as $p) {
                if ($p == 1) $orX->add('p.prix < 50');
                if ($p == 2) $orX->add('p.prix >= 50 AND p.prix < 100');
                if ($p == 3) $orX->add('p.prix >= 100 AND p.prix < 200');
                if ($p == 4) $orX->add('p.prix >= 200');
            }
            $qb->andWhere($orX);
        }

        // Filtres note
        if ($note) {
            $qb->andWhere('p.note >= :note')
               ->setParameter('note', $note);
        }

        // Tri
        if ($sort === 'price_asc') {
            $qb->orderBy('p.prix', 'ASC');
        } elseif ($sort === 'price_desc') {
            $qb->orderBy('p.prix', 'DESC');
        } elseif ($sort === 'popularity') {
            $qb->orderBy('p.nombreAvis', 'DESC');
        } else {
            $qb->orderBy('p.createdAt', 'DESC');
        }

        return $qb->getQuery();
    }

    /**
     * Retourne le nombre total de produits
     */
    public function countPublished()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les statistiques de notes (note => nombre de produits)
     */
    public function getNotesStats()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.note as note, COUNT(p.id) as nb')
            ->groupBy('p.note');

        $result = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            $result[$row['note']] = $row['nb'];
        }
        return $result;
    }

    /**
     * Retourne toutes les catégories distinctes avec le nombre de produits
     */
    public function findAllCategoriesWithProductCount()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.categorie, COUNT(p.id) as nbProduits')
            ->groupBy('p.categorie');

        $result = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            $result[] = [
                'nom' => $row['categorie'],
                'nbProduits' => $row['nbProduits'],
            ];
        }
        return $result;
    }
}

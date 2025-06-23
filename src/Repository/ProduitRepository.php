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
}

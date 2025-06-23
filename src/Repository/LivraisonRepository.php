<?php

namespace App\Repository;

use App\Entity\Livraison;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livraison::class);
    }

    public function findLivraisonsEnAttente(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.statut = :statut')
            ->setParameter('statut', 'en_attente')
            ->orderBy('l.dateLivraison', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLivraisonsPourLivreur(Utilisateur $livreur): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.livreur = :livreur')
            ->setParameter('livreur', $livreur)
            ->orderBy('l.dateLivraison', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

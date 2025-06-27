<?php
namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Récupère les notifications non lues pour un utilisateur
     */
    public function findUnreadByUser(Utilisateur $user): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.destinataire = :user AND n.estLue = false')
            ->setParameter('user', $user)
            ->orderBy('n.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les notifications par type pour un utilisateur
     */
    public function findByTypeAndUser(string $type, Utilisateur $user): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.destinataire = :user AND n.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->orderBy('n.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Marque toutes les notifications comme lues pour un utilisateur
     */
    public function markAllAsRead(Utilisateur $user): int
    {
        return $this->createQueryBuilder('n')
            ->update()
            ->set('n.estLue', true)
            ->where('n.destinataire = :user AND n.estLue = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
} 
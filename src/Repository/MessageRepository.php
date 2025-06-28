<?php
namespace App\Repository;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Récupère les messages d'une conversation entre deux utilisateurs
     */
    public function findConversation(Utilisateur $user1, Utilisateur $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.expediteur = :user1 AND m.destinataire = :user2) OR (m.expediteur = :user2 AND m.destinataire = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les conversations d'un utilisateur avec le dernier message de chaque conversation
     */
    public function findConversationsForUser(Utilisateur $user): array
    {
        $qb = $this->createQueryBuilder('m');
        
        // Sous-requête pour obtenir le dernier message de chaque conversation
        $subQuery = $this->createQueryBuilder('m2')
            ->select('MAX(m2.dateEnvoi)')
            ->where('(m2.expediteur = :user AND m2.destinataire = u) OR (m2.expediteur = u AND m2.destinataire = :user)')
            ->getDQL();
        
        $conversations = $qb->select('u', 'm')
            ->leftJoin('App\Entity\Utilisateur', 'u', 'WITH', 
                '(m.expediteur = :user AND m.destinataire = u) OR (m.expediteur = u AND m.destinataire = :user)')
            ->where('m.dateEnvoi = (' . $subQuery . ')')
            ->andWhere('u.id != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        
        // Compter les messages non lus pour chaque conversation
        $result = [];
        foreach ($conversations as $conversation) {
            $otherUser = $conversation['u'];
            $lastMessage = $conversation['m'];
            
            $unreadCount = $this->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.destinataire = :user AND m.expediteur = :other AND m.estLu = false')
                ->setParameter('user', $user)
                ->setParameter('other', $otherUser)
                ->getQuery()
                ->getSingleScalarResult();
            
            $result[] = [
                'other_user' => $otherUser,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount
            ];
        }
        
        return $result;
    }

    /**
     * Marque tous les messages d'une conversation comme lus
     */
    public function markMessagesAsRead(Utilisateur $user, Utilisateur $other): void
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.estLu', true)
            ->where('m.destinataire = :user AND m.expediteur = :other AND m.estLu = false')
            ->setParameter('user', $user)
            ->setParameter('other', $other)
            ->getQuery()
            ->execute();
    }

    /**
     * Récupère les messages non lus pour un utilisateur
     */
    public function findUnreadMessages(Utilisateur $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.destinataire = :user AND m.estLu = false')
            ->setParameter('user', $user)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 
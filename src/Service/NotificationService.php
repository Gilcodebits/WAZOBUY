<?php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Crée une notification pour un utilisateur
     */
    public function createNotification(
        Utilisateur $destinataire,
        string $titre,
        string $message,
        string $type = 'system',
        ?string $lien = null,
        ?Utilisateur $auteur = null
    ): Notification {
        $notification = new Notification();
        $notification->setDestinataire($destinataire);
        $notification->setTitre($titre);
        $notification->setMessage($message);
        $notification->setType($type);
        $notification->setLien($lien);
        $notification->setAuteur($auteur);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    /**
     * Crée une notification de nouvelle commande
     */
    public function notifyNewOrder(Utilisateur $vendeur, string $commandeId): Notification
    {
        return $this->createNotification(
            $vendeur,
            'Nouvelle commande reçue',
            "Vous avez reçu une nouvelle commande #{$commandeId}",
            'commande',
            "/admin/commandes/{$commandeId}"
        );
    }

    /**
     * Crée une notification de nouveau message
     */
    public function notifyNewMessage(Utilisateur $destinataire, Utilisateur $expediteur): Notification
    {
        return $this->createNotification(
            $destinataire,
            'Nouveau message',
            "Vous avez reçu un nouveau message de {$expediteur->getNom()} {$expediteur->getPrenom()}",
            'message',
            '/admin/chat',
            $expediteur
        );
    }

    /**
     * Crée une notification de promotion
     */
    public function notifyPromotion(Utilisateur $destinataire, string $titre, string $description): Notification
    {
        return $this->createNotification(
            $destinataire,
            'Nouvelle promotion',
            $description,
            'promotion',
            '/admin/produits'
        );
    }

    /**
     * Crée une notification système
     */
    public function notifySystem(Utilisateur $destinataire, string $titre, string $message): Notification
    {
        return $this->createNotification(
            $destinataire,
            $titre,
            $message,
            'system'
        );
    }

    /**
     * Notifie tous les vendeurs
     */
    public function notifyAllSellers(string $titre, string $message, string $type = 'system'): array
    {
        $vendeurs = $this->entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_SELLER%')
            ->getQuery()
            ->getResult();

        $notifications = [];
        foreach ($vendeurs as $vendeur) {
            $notifications[] = $this->createNotification($vendeur, $titre, $message, $type);
        }

        return $notifications;
    }
} 
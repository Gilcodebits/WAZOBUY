<?php
namespace App\Controller\Admin;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/notifications')]
class NotificationController extends AbstractController
{
    #[Route('', name: 'admin_notifications', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $notifs = $em->getRepository(Notification::class)->findBy(
            ['destinataire' => $user->getUserIdentifier()],
            ['estLu' => 'ASC', 'dateCreation' => 'DESC'],
            20
        );
        $result = [];
        foreach ($notifs as $notif) {
            $result[] = [
                'id' => $notif->getId(),
                'titre' => $notif->getTitre(),
                'message' => $notif->getMessage(),
                'date' => $notif->getDateCreation()->format('d/m/Y H:i'),
                'estLu' => $notif->isEstLu(),
                'lien' => $notif->getLien(),
                'type' => $notif->getType(),
            ];
        }
        return $this->json($result);
    }

    #[Route('/read/{id}', name: 'admin_notification_read', methods: ['POST'])]
    public function read(EntityManagerInterface $em, int $id): JsonResponse
    {
        $notif = $em->getRepository(Notification::class)->find($id);
        if ($notif) {
            $notif->setEstLu(true);
            $em->flush();
        }
        return $this->json(['success' => true]);
    }

    #[Route('/read-all', name: 'admin_notification_read_all', methods: ['POST'])]
    public function readAll(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $notifs = $em->getRepository(Notification::class)->findBy([
            'destinataire' => $user->getUserIdentifier(),
            'estLu' => false
        ]);
        foreach ($notifs as $notif) {
            $notif->setEstLu(true);
        }
        $em->flush();
        return $this->json(['success' => true]);
    }
} 
<?php
namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/notifications')]
#[IsGranted('ROLE_SELLER')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'admin_notifications_index')]
    public function index(): Response
    {
        return $this->render('admin/notifications/index.html.twig');
    }

    #[Route('/list', name: 'admin_notifications_list', methods: ['GET'])]
    public function getNotifications(NotificationRepository $notificationRepository): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(
            ['destinataire' => $user],
            ['dateCreation' => 'DESC'],
            20
        );

        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'titre' => $notification->getTitre(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'estLue' => $notification->isEstLue(),
                'dateCreation' => $notification->getDateCreation()->format('d/m/Y H:i'),
                'lien' => $notification->getLien(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/mark-read/{id}', name: 'admin_notifications_mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($notification->getDestinataire()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Accès non autorisé'], 403);
        }

        $notification->setEstLue(true);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/mark-all-read', name: 'admin_notifications_mark_all_read', methods: ['POST'])]
    public function markAllAsRead(NotificationRepository $notificationRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy([
            'destinataire' => $user,
            'estLue' => false
        ]);

        foreach ($notifications as $notification) {
            $notification->setEstLue(true);
        }
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/unread-count', name: 'admin_notifications_unread_count', methods: ['GET'])]
    public function getUnreadCount(NotificationRepository $notificationRepository): JsonResponse
    {
        $user = $this->getUser();
        $count = $notificationRepository->count([
            'destinataire' => $user,
            'estLue' => false
        ]);

        return new JsonResponse(['count' => $count]);
    }
} 
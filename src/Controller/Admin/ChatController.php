<?php
namespace App\Controller\Admin;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/chat')]
class ChatController extends AbstractController
{
    #[Route('/conversations', name: 'admin_chat_conversations', methods: ['GET'])]
    public function conversations(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $userId = $user->getUserIdentifier();
        $repo = $em->getRepository(Message::class);
        $userRepo = $em->getRepository(\App\Entity\Utilisateur::class);
        // Récupérer toutes les conversations où l'utilisateur est impliqué
        $convs = $repo->createQueryBuilder('m')
            ->select('m.conversationId, MAX(m.dateEnvoi) as lastDate')
            ->where('m.destinataire = :user OR m.expediteur = :user')
            ->setParameter('user', $userId)
            ->groupBy('m.conversationId')
            ->orderBy('lastDate', 'DESC')
            ->getQuery()->getResult();
        $result = [];
        foreach ($convs as $conv) {
            $convId = $conv['conversationId'];
            // Dernier message
            $lastMsg = $repo->findOneBy(['conversationId' => $convId], ['dateEnvoi' => 'DESC']);
            // Interlocuteur (autre que moi)
            $otherEmail = $lastMsg->getExpediteur() === $userId ? $lastMsg->getDestinataire() : $lastMsg->getExpediteur();
            $other = $userRepo->findOneBy(['email' => $otherEmail]);
            // Nombre de non lus
            $unread = $repo->count([
                'conversationId' => $convId,
                'destinataire' => $userId,
                'estLu' => false
            ]);
            $result[] = [
                'conversationId' => $convId,
                'nom' => $other ? $other->getNom() : '',
                'prenom' => $other ? $other->getPrenom() : '',
                'photoProfil' => $other && $other->getPhotoProfil() ? '/' . $other->getPhotoProfil() : null,
                'dernierMessage' => $lastMsg ? $lastMsg->getContenu() : '',
                'dateDernierMessage' => $lastMsg ? $lastMsg->getDateEnvoi()->format('d/m/Y H:i') : '',
                'unreadCount' => $unread
            ];
        }
        return $this->json($result);
    }

    #[Route('/messages/{conversationId}', name: 'admin_chat_messages', methods: ['GET'])]
    public function messages(EntityManagerInterface $em, string $conversationId): JsonResponse
    {
        $user = $this->getUser();
        $messages = $em->getRepository(Message::class)->findBy([
            'conversationId' => $conversationId
        ], ['dateEnvoi' => 'ASC']);
        $userRepo = $em->getRepository(\App\Entity\Utilisateur::class);
        $result = [];
        foreach ($messages as $msg) {
            $exp = $userRepo->findOneBy(['email' => $msg->getExpediteur()]);
            $result[] = [
                'id' => $msg->getId(),
                'expediteur' => $msg->getExpediteur(),
                'destinataire' => $msg->getDestinataire(),
                'contenu' => $msg->getContenu(),
                'dateEnvoi' => $msg->getDateEnvoi()->format('d/m/Y H:i'),
                'isMe' => $msg->getExpediteur() === $user->getUserIdentifier(),
                'nom' => $exp ? $exp->getNom() : '',
                'prenom' => $exp ? $exp->getPrenom() : '',
                'photoProfil' => $exp && $exp->getPhotoProfil() ? '/' . $exp->getPhotoProfil() : null,
            ];
        }
        return $this->json($result);
    }

    #[Route('/message', name: 'admin_chat_send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $message = new Message();
        $message->setExpediteur($user->getUserIdentifier());
        $message->setDestinataire($data['destinataire']);
        $message->setContenu($data['contenu']);
        $message->setDateEnvoi(new \DateTimeImmutable());
        $message->setConversationId($data['conversationId'] ?? null);
        $em->persist($message);
        $em->flush();
        return $this->json(['success' => true, 'message' => $message]);
    }

    #[Route('/messages/read/{conversationId}', name: 'admin_chat_mark_read', methods: ['POST'])]
    public function markRead(EntityManagerInterface $em, string $conversationId): JsonResponse
    {
        $user = $this->getUser();
        $messages = $em->getRepository(Message::class)->findBy([
            'conversationId' => $conversationId,
            'destinataire' => $user->getUserIdentifier(),
            'estLu' => false
        ]);
        foreach ($messages as $msg) {
            $msg->setEstLu(true);
        }
        $em->flush();
        return $this->json(['success' => true]);
    }
} 
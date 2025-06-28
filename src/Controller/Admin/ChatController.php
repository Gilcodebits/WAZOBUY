<?php
namespace App\Controller\Admin;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/chat')]
#[IsGranted('ROLE_VENDEUR')]
class ChatController extends AbstractController
{
    #[Route('/', name: 'admin_chat_index')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        $conversations = $messageRepository->findConversationsForUser($user);
        
        return $this->render('admin/messages/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/conversations', name: 'admin_chat_conversations', methods: ['GET'])]
    public function getConversations(MessageRepository $messageRepository): JsonResponse
    {
        $user = $this->getUser();
        $conversations = $messageRepository->findConversationsForUser($user);
        
        $data = [];
        foreach ($conversations as $conversation) {
            $data[] = [
                'id' => $conversation['other_user']->getId(),
                'name' => $conversation['other_user']->getNom() . ' ' . $conversation['other_user']->getPrenom(),
                'lastMessage' => $conversation['last_message']->getContenu(),
                'date' => $conversation['last_message']->getDateEnvoi()->format('d/m/Y H:i'),
                'unreadCount' => $conversation['unread_count'],
                'isFromMe' => $conversation['last_message']->getExpediteur()->getId() === $user->getId(),
            ];
        }
        
        return new JsonResponse($data);
    }

    #[Route('/messages', name: 'admin_chat_messages', methods: ['GET'])]
    public function getMessages(Request $request, MessageRepository $messageRepository, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $user = $this->getUser();
        $otherId = $request->query->get('user_id');
        $other = $utilisateurRepository->find($otherId);
        
        if (!$other) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }
        
        // Marquer les messages comme lus
        $messageRepository->markMessagesAsRead($user, $other);
        
        $messages = $messageRepository->findConversation($user, $other);
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'from' => $message->getExpediteur()->getId(),
                'to' => $message->getDestinataire()->getId(),
                'content' => $message->getContenu(),
                'date' => $message->getDateEnvoi()->format('d/m/Y H:i'),
                'isMe' => $message->getExpediteur()->getId() === $user->getId(),
            ];
        }
        return new JsonResponse($data);
    }

    #[Route('/send', name: 'admin_chat_send', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $user = $this->getUser();
        $otherId = $request->request->get('user_id');
        $content = $request->request->get('content');
        $other = $utilisateurRepository->find($otherId);
        
        if (!$other || !$content) {
            return new JsonResponse(['error' => 'Paramètres manquants'], 400);
        }
        
        $message = new Message();
        $message->setExpediteur($user);
        $message->setDestinataire($other);
        $message->setContenu($content);
        $message->setDateEnvoi(new \DateTimeImmutable());
        $message->setEstLu(false);
        
        $em->persist($message);
        $em->flush();
        
        return new JsonResponse(['success' => true]);
    }
} 
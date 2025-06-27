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
#[IsGranted('ROLE_SELLER')]
class ChatController extends AbstractController
{
    #[Route('/', name: 'admin_chat_index')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findAll();
        return $this->render('admin/messages/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
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
        $messages = $messageRepository->findConversation($user, $other);
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'from' => $message->getExpediteur()->getId(),
                'to' => $message->getDestinataire()->getId(),
                'content' => $message->getContenu(),
                'date' => $message->getDateEnvoi()->format('Y-m-d H:i'),
                'isMe' => $message->getExpediteur()->getId() === ($user instanceof \App\Entity\Utilisateur ? $user->getId() : null),
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
        $em->persist($message);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }
} 
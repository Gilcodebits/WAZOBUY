<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\ParametreGeneral;
use Doctrine\ORM\EntityManagerInterface;

#[IsGranted('ROLE_ADMIN')]
class SettingsController extends AbstractController
{
    #[Route('/admin/settings', name: 'app_admin_settings')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // On suppose qu'il n'y a qu'un seul enregistrement de paramètres généraux
        $repo = $em->getRepository(ParametreGeneral::class);
        $settings = $repo->findOneBy([]);
        if (!$settings) {
            $settings = new ParametreGeneral();
            $em->persist($settings);
            $em->flush();
        }
        
        if ($request->isMethod('POST')) {
            // Paramètres d'apparence
            $settings->setTheme($request->request->get('theme', $settings->getTheme()));
            $settings->setLangue($request->request->get('langue', $settings->getLangue()));
            $settings->setFuseau($request->request->get('fuseau', $settings->getFuseau()));
            $settings->setElementsParPage((int)$request->request->get('elements_par_page', $settings->getElementsParPage()));
            $settings->setAnimations($request->request->has('animations'));
            
            // Paramètres de notifications
            $settings->setNotificationsEmail($request->request->has('notifications_email'));
            $settings->setNotifCommandes($request->request->has('notif_commandes'));
            $settings->setNotifLitiges($request->request->has('notif_litiges'));
            $settings->setNotifVendeurs($request->request->has('notif_vendeurs'));
            $settings->setNotifMessages($request->request->has('notif_messages'));
            $settings->setNotifRapport($request->request->get('notif_rapport', $settings->getNotifRapport()));
            
            // Paramètres de sécurité
            $settings->setDeuxFacteurs($request->request->has('2fa'));
            $settings->setNotifConnexion($request->request->has('notif_connexion'));
            $settings->setSessionExp((int)$request->request->get('session_exp', $settings->getSessionExp()));
            
            $em->flush();
            $this->addFlash('success', 'Paramètres globaux sauvegardés !');
            return $this->redirectToRoute('app_admin_settings');
        }
        
        return $this->render('admin/settings.html.twig', [
            'user' => $user,
            'settings' => [
                // Paramètres d'apparence
                'theme' => $settings->getTheme(),
                'langue' => $settings->getLangue(),
                'fuseau' => $settings->getFuseau(),
                'elements_par_page' => $settings->getElementsParPage(),
                'animations' => $settings->getAnimations(),
                
                // Paramètres de notifications
                'notifications_email' => $settings->getNotificationsEmail(),
                'notif_commandes' => $settings->getNotifCommandes(),
                'notif_litiges' => $settings->getNotifLitiges(),
                'notif_vendeurs' => $settings->getNotifVendeurs(),
                'notif_messages' => $settings->getNotifMessages(),
                'notif_rapport' => $settings->getNotifRapport(),
                
                // Paramètres de sécurité
                '2fa' => $settings->getDeuxFacteurs(),
                'notif_connexion' => $settings->getNotifConnexion(),
                'session_exp' => $settings->getSessionExp(),
            ]
        ]);
    }
} 
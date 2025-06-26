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
            $settings->setTheme($request->request->get('theme', $settings->getTheme()));
            $settings->setLangue($request->request->get('langue', $settings->getLangue()));
            $settings->setFuseau($request->request->get('fuseau', $settings->getFuseau()));
            $settings->setElementsParPage((int)$request->request->get('elements_par_page', $settings->getElementsParPage()));
            // ... autres paramètres à ajouter ici
            $em->flush();
            $this->addFlash('success', 'Paramètres globaux sauvegardés !');
            return $this->redirectToRoute('app_admin_settings');
        }
        return $this->render('admin/settings.html.twig', [
            'user' => $user,
            'settings' => [
                'theme' => $settings->getTheme(),
                'langue' => $settings->getLangue(),
                'fuseau' => $settings->getFuseau(),
                'elements_par_page' => $settings->getElementsParPage(),
                // ... autres paramètres à passer à la vue
            ]
        ]);
    }
} 
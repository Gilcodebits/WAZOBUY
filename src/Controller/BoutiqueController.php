<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    #[IsGranted('ROLE_VENDEUR', message: 'Vous devez être vendeur pour accéder à cette page')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur a le rôle vendeur
        if (!$this->isGranted('ROLE_VENDEUR')) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('seller/sellerdashbord.html.twig', [
            'user' => $user
        ]);
    }
}
?>

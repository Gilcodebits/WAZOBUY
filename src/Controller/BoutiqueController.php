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
        // Rediriger vers le nouveau dashboard vendeur
        return $this->redirectToRoute('app_seller_dashboard');
    }
}
?>

<?php
namespace App\Controller\Api;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class FavoriController extends AbstractController
{
    #[Route('/api/favori/{id}', name: 'api_favori_toggle', methods: ['POST'])]
    public function toggle($id, ProduitRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) return $this->json(['error' => 'Non connecté'], 403);
        $produit = $repo->find($id);
        if (!$produit) return $this->json(['error' => 'Produit non trouvé'], 404);

        $isFavori = false;
        if (method_exists($user, 'getFavoris')) {
            if ($user->getFavoris()->contains($produit)) {
                $user->removeFavori($produit);
                $isFavori = false;
            } else {
                $user->addFavori($produit);
                $isFavori = true;
            }
            $em->persist($user);
            $em->flush();
            $em->refresh($user);
        }
        return $this->json(['favori' => $isFavori]);
    }
} 
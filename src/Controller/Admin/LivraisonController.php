<?php

namespace App\Controller\Admin;

use App\Entity\Livraison;
use App\Repository\LivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivraisonController extends AbstractController
{
    #[Route('/admin/livraisons', name: 'app_admin_livraisons')]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        // Récupérer toutes les livraisons
        $livraisons = $livraisonRepository
            ->createQueryBuilder('l')
            ->orderBy('l.dateLivraison', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/livraisons/index.html.twig', [
            'livraisons' => $livraisons
        ]);
    }

    #[Route('/admin/livraisons/{id}', name: 'app_admin_livraison_show', requirements: ['id' => '\d+'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('admin/livraisons/show.html.twig', [
            'livraison' => $livraison
        ]);
    }

    #[Route('/admin/livraisons/{id}/valider', name: 'app_admin_livraison_valider', requirements: ['id' => '\d+'])]
    public function valider(Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        $livraison->setStatut('validée');
        $entityManager->flush();

        $this->addFlash('success', 'La livraison a été validée avec succès.');
        return $this->redirectToRoute('app_admin_livraisons');
    }

    #[Route('/admin/livraisons/{id}/annuler', name: 'app_admin_livraison_annuler', requirements: ['id' => '\d+'])]
    public function annuler(Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        $livraison->setStatut('annulée');
        $entityManager->flush();

        $this->addFlash('success', 'La livraison a été annulée avec succès.');
        return $this->redirectToRoute('app_admin_livraisons');
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'app_admin_utilisateurs')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        // Récupérer tous les utilisateurs (sauf les admins)
        $utilisateurs = $utilisateurRepository
            ->createQueryBuilder('u')
            ->where('u.roles NOT LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();

        // Statistiques utilisateurs
        $stats = [
            'total' => count($utilisateurRepository->findAll()),
            'clients' => count($utilisateurRepository->findByRole('ROLE_CLIENT')),
            'vendeurs' => count($utilisateurRepository->findByRole('ROLE_VENDEUR')),
            'livreurs' => count($utilisateurRepository->findByRole('ROLE_LIVREUR')),
            'admins' => count($utilisateurRepository->findByRole('ROLE_ADMIN')),
        ];

        return $this->render('admin/utilisateurs/index.html.twig', [
            'utilisateurs' => $utilisateurs,
            'stats' => $stats
        ]);
    }

    #[Route('/admin/utilisateurs/{id}', name: 'app_admin_utilisateur_show', requirements: ['id' => '\d+'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/utilisateurs/show.html.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/activer', name: 'app_admin_utilisateur_activer', requirements: ['id' => '\d+'])]
    public function activer(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $utilisateur->setEstActif(true);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été activé avec succès.');
        return $this->redirectToRoute('app_admin_utilisateurs');
    }

    #[Route('/admin/utilisateurs/{id}/desactiver', name: 'app_admin_utilisateur_desactiver', requirements: ['id' => '\d+'])]
    public function desactiver(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $utilisateur->setEstActif(false);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été désactivé avec succès.');
        return $this->redirectToRoute('app_admin_utilisateurs');
    }
}

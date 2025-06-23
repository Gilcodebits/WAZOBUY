<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/admin/commandes', name: 'app_admin_commandes')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        // Récupérer toutes les commandes
        $commandes = $commandeRepository
            ->createQueryBuilder('c')
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/commandes/index.html.twig', [
            'commandes' => $commandes
        ]);
    }

    #[Route('/admin/commandes/{id}', name: 'app_admin_commande_show', requirements: ['id' => '\d+'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commandes/show.html.twig', [
            'commande' => $commande
        ]);
    }

    #[Route('/admin/commandes/{id}/valider', name: 'app_admin_commande_valider', requirements: ['id' => '\d+'])]
    public function valider(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setStatut('validée');
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été validée avec succès.');
        return $this->redirectToRoute('app_admin_commandes');
    }

    #[Route('/admin/commandes/{id}/annuler', name: 'app_admin_commande_annuler', requirements: ['id' => '\d+'])]
    public function annuler(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setStatut('annulée');
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été annulée avec succès.');
        return $this->redirectToRoute('app_admin_commandes');
    }

    #[Route('/admin/commandes/{id}/resolve', name: 'app_admin_commande_resolve', methods: ['POST'])]
    public function resolve(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($commande->getStatut() === 'litige') {
            $commande->setStatut('résolu');
            $entityManager->flush();
            
            $this->addFlash('success', 'Le litige a été résolu avec succès.');
        } else {
            $this->addFlash('error', 'Cette commande n\'est pas en litige.');
        }

        return $this->redirectToRoute('app_admin_commande_show', ['id' => $commande->getId()]);
    }
}

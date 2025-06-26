<?php

namespace App\Controller\Admin;

use App\Entity\Livraison;
use App\Repository\LivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ParametreGeneral;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use DateTimeImmutable;

class LivraisonController extends AbstractController
{
    #[Route('/admin/livraisons', name: 'app_admin_livraisons')]
    public function index(Request $request, LivraisonRepository $livraisonRepository, EntityManagerInterface $em): Response
    {
        $param = $em->getRepository(ParametreGeneral::class)->findOneBy([]);
        $perPage = $param ? $param->getElementsParPage() : 10;
        $page = max(1, (int)$request->query->get('page', 1));
        $statut = $request->query->get('statut');
        $q = $request->query->get('q');
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');

        $qb = $livraisonRepository->createQueryBuilder('l')
            ->orderBy('l.dateLivraison', 'DESC');
        if ($statut) {
            $qb->andWhere('l.statut = :statut')->setParameter('statut', $statut);
        }
        if ($q) {
            $qb->leftJoin('l.commande', 'c')
               ->leftJoin('c.client', 'cli')
               ->leftJoin('l.livreur', 'liv')
               ->andWhere('c.id LIKE :q OR cli.nom LIKE :q OR cli.prenom LIKE :q OR l.ville LIKE :q OR l.codeLivraison LIKE :q OR liv.nom LIKE :q OR liv.prenom LIKE :q')
               ->setParameter('q', '%'.$q.'%');
        }
        if ($dateDebut) {
            $qb->andWhere('l.dateLivraison >= :dateDebut')->setParameter('dateDebut', new DateTimeImmutable($dateDebut.' 00:00:00'));
        }
        if ($dateFin) {
            $qb->andWhere('l.dateLivraison <= :dateFin')->setParameter('dateFin', new DateTimeImmutable($dateFin.' 23:59:59'));
        }
        $qb->setFirstResult(($page-1)*$perPage)
           ->setMaxResults($perPage);
        $paginator = new Paginator($qb);
        $total = count($paginator);
        $livraisons = iterator_to_array($paginator);
        // Statistiques
        $all = $livraisonRepository->createQueryBuilder('l')->getQuery()->getResult();
        $stats = [
            'total' => count($all),
            'en_attente' => count(array_filter($all, fn($l) => $l->getStatut() === 'en_attente')),
            'validee' => count(array_filter($all, fn($l) => $l->getStatut() === 'validée')),
            'annulee' => count(array_filter($all, fn($l) => $l->getStatut() === 'annulée')),
            'expediee' => count(array_filter($all, fn($l) => $l->getStatut() === 'expédiée')),
            'chiffre_affaires' => array_sum(array_map(fn($l) => $l->getFraisLivraison(), $all)),
        ];
        return $this->render('admin/livraisons/index.html.twig', [
            'livraisons' => $livraisons,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'statut' => $statut,
            'q' => $q,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'stats' => $stats
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

<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits d\'accès à cette page')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur a le rôle admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }

        // Récupérer les statistiques nécessaires
        $utilisateursTotal = $entityManager
            ->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $vendeursTotal = $entityManager
            ->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_VENDEUR"%')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $produitsTotal = $entityManager
            ->getRepository(Produit::class)
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $commandesTotal = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $chiffreAffaires = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->select('SUM(c.montantTotal)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Croissance des commandes (par rapport à hier)
        $hier = (new \DateTimeImmutable('yesterday'))->setTime(0, 0, 0);
        $aujourdhui = (new \DateTimeImmutable())->setTime(0, 0, 0);

        $commandesHier = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.dateCommande >= :hier')
            ->andWhere('c.dateCommande < :aujourdhui')
            ->setParameter('hier', $hier)
            ->setParameter('aujourdhui', $aujourdhui)
            ->getQuery()
            ->getSingleScalarResult();

        $commandesAujourdhui = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.dateCommande >= :aujourdhui')
            ->setParameter('aujourdhui', $aujourdhui)
            ->getQuery()
            ->getSingleScalarResult();

        if ($commandesHier > 0) {
            $croissanceCommandes = (($commandesAujourdhui - $commandesHier) / $commandesHier) * 100;
        } else {
            $croissanceCommandes = $commandesAujourdhui > 0 ? 100 : 0;
        }

        $dateAujourdhui = (new \DateTimeImmutable())->format('d/m/Y');

        $commandesRecentes = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->orderBy('c.dateCommande', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $litiges = $entityManager
            ->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'litige')
            ->orderBy('c.dateCommande', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $nouveauxVendeurs = $entityManager
            ->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_VENDEUR"%')
            ->orderBy('u.dateCreation', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Activités récentes (vide)
        $activitesRecentes = [];

        return $this->render('admin/dashboard.html.twig', [
            'utilisateursTotal' => $utilisateursTotal,
            'vendeursTotal' => $vendeursTotal,
            'produitsTotal' => $produitsTotal,
            'commandesTotal' => $commandesTotal,
            'dateAujourdhui' => $dateAujourdhui,
            'chiffreAffaires' => $chiffreAffaires,
            'croissanceCommandes' => $croissanceCommandes,
            'commandesRecentes' => $commandesRecentes,
            'litiges' => $litiges,
            'nouveauxVendeurs' => $nouveauxVendeurs,
            'activitesRecentes' => $activitesRecentes,
            'user' => $user
        ]);
    }
}
?>

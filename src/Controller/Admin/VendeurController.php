<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class VendeurController extends AbstractController
{
    #[Route('/admin/vendeurs', name: 'app_admin_vendeurs')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        // Récupérer tous les vendeurs
        $vendeurs = $utilisateurRepository
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_VENDEUR%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/vendeurs/index.html.twig', [
            'vendeurs' => $vendeurs
        ]);
    }

    #[Route('/admin/vendeurs/{id}', name: 'app_admin_vendeur_show', requirements: ['id' => '\d+'])]
    public function show(Utilisateur $vendeur): Response
    {
        return $this->render('admin/vendeurs/show.html.twig', [
            'vendeur' => $vendeur
        ]);
    }

    #[Route('/admin/vendeurs/ajouter', name: 'app_admin_vendeur_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $vendeur = new Utilisateur();
        $vendeur->setRoles(['ROLE_VENDEUR']);
        $form = $this->createForm(RegistrationFormType::class, $vendeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($vendeur, $vendeur->getPlainPassword());
            $vendeur->setPassword($hashedPassword);
            $entityManager->persist($vendeur);
            $entityManager->flush();
            $this->addFlash('success', 'Vendeur ajouté avec succès !');
            return $this->redirectToRoute('app_admin_vendeurs');
        }

        return $this->render('admin/vendeurs/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

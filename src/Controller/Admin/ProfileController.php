<?php
namespace App\Controller\Admin;

use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', message: 'Accès réservé aux administrateurs.')]
class ProfileController extends AbstractController
{
    #[Route('/admin/profile', name: 'app_admin_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        return $this->render('admin/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/profile/change-password', name: 'app_admin_profile_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        /** @var \App\Entity\Utilisateur $user */
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('danger', 'Ancien mot de passe incorrect.');
            } elseif (strlen($newPassword) < 6) {
                $this->addFlash('danger', 'Le nouveau mot de passe doit contenir au moins 6 caractères.');
            } else {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $em->flush();
                $this->addFlash('success', 'Mot de passe modifié avec succès.');
                return $this->redirectToRoute('app_admin_profile');
            }
        }

        return $this->render('admin/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/profile/edit', name: 'app_admin_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        /** @var \App\Entity\Utilisateur $user */
        $form = $this->createForm(ProfileType::class, [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPrenom($data['prenom']);
            $user->setNom($data['nom']);
            $user->setEmail($data['email']);
            $photoFile = $form['photoProfil']->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/profile',
                    $newFilename
                );
                $user->setPhotoProfil('uploads/profile/'.$newFilename);
            }
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_admin_profile');
        }
        return $this->render('admin/profile_edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/admin/profile/upload-photo', name: 'app_admin_profile_upload_photo', methods: ['POST'])]
    public function uploadPhoto(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        /** @var \App\Entity\Utilisateur $user */
        $photoFile = $request->files->get('photoProfil');
        if ($photoFile && $photoFile->isValid()) {
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move(
                $this->getParameter('kernel.project_dir').'/public/uploads/profile',
                $newFilename
            );
            $user->setPhotoProfil('uploads/profile/'.$newFilename);
            $em->flush();
            $this->addFlash('success', 'Photo de profil mise à jour !');
        } else {
            $this->addFlash('danger', 'Erreur lors de l\'upload de la photo.');
        }
        return $this->redirectToRoute('app_admin_profile');
    }
} 
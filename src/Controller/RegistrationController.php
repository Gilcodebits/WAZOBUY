<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new Utilisateur();
        $user->setRoles(['ROLE_CLIENT']);
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Vérifier que les mots de passe correspondent
                $plainPassword = $form->get('plainPassword')->getData();
                $confirmPassword = $form->get('confirmPassword')->getData();
                
                if ($plainPassword !== $confirmPassword) {
                    $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                    return $this->redirectToRoute('app_register');
                }
                
                if (!$form->get('agreeTerms')->getData()) {
                    $this->addFlash('error', 'Vous devez accepter les conditions d\'utilisation');
                    return $this->redirectToRoute('app_register');
                }
                
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );
                
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash('success', 'Inscription réussie !');
                return $this->redirectToRoute('app_login');
            }
            
            // Gestion des boutons sociaux
            if ($request->request->has('google')) {
                return $this->redirectToRoute('hwi_oauth_service_redirect', ['service' => 'google']);
            }
            if ($request->request->has('facebook')) {
                return $this->redirectToRoute('hwi_oauth_service_redirect', ['service' => 'facebook']);
            }
        }
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
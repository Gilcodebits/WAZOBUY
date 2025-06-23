<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientProfileController extends AbstractController
{
    #[Route('/client/profile', name: 'app_client_profile')]
    public function index(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $formFactory->create(ProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_client_profile');
        }

        return $this->render('clientdashbord/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

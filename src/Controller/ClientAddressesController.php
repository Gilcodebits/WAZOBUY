<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientAddressesController extends AbstractController
{
    #[Route('/client/addresses', name: 'app_client_addresses')]
    public function index(AdresseRepository $adresseRepository): Response
    {
        $user = $this->getUser();
        $adresses = $adresseRepository->findBy(['utilisateur' => $user]);

        return $this->render('clientdashbord/addresses.html.twig', [
            'adresses' => $adresses
        ]);
    }

    #[Route('/client/addresses/new', name: 'app_client_addresses_new')]
    public function new(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $adresse = new Adresse();
        $form = $formFactory->create(AdresseType::class, $adresse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setUtilisateur($this->getUser());
            $entityManager->persist($adresse);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle adresse ajoutée avec succès');
            return $this->redirectToRoute('app_client_addresses');
        }

        return $this->render('clientdashbord/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/client/addresses/{id}/edit', name: 'app_client_addresses_edit')]
    public function edit(Request $request, Adresse $adresse, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $formFactory->create(AdresseType::class, $adresse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Adresse mise à jour avec succès');
            return $this->redirectToRoute('app_client_addresses');
        }

        return $this->render('clientdashbord/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/client/addresses/{id}/delete', name: 'app_client_addresses_delete')]
    public function delete(Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($adresse);
        $entityManager->flush();

        $this->addFlash('success', 'Adresse supprimée avec succès');
        return $this->redirectToRoute('app_client_addresses');
    }
}

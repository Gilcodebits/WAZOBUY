<?php
namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/vendeur/produit/ajouter', name: 'seller_product_add')]
    #[IsGranted('ROLE_VENDEUR')]
    public function addProduct(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProductType::class, $produit);

        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Gérer l'upload des images
                $imageFile = $form->get('images')->getData();
                
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('products_directory'),
                            $newFilename
                        );
                        $produit->setImage($newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
                        return $this->render('seller/product_add.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }
                }

                // Initialiser les champs par défaut
                $produit->setVendeur($this->getUser());
                $produit->setPrixOriginal($produit->getPrix());
                $produit->setNouveau(true);
                $produit->setEnPromotion(false);
                $produit->setNote(0);
                $produit->setNombreAvis(0);
                $produit->setCaracteristiques([]);

                try {
                    $em->persist($produit);
                    $em->flush();

                    $this->addFlash('success', 'Produit ajouté avec succès !');
                    return $this->redirectToRoute('app_boutique');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
                }
            } else {
                // Afficher les erreurs de validation
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                $this->addFlash('error', 'Erreurs de validation: ' . implode(', ', $errors));
            }
        }

        return $this->render('seller/product_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 
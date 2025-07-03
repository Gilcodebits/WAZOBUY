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
                
                // Gérer la promotion
                if ($produit->isPromotion() && $produit->getPourcentagePromotion()) {
                    $pourcentage = $produit->getPourcentagePromotion();
                    $prixOriginal = $produit->getPrix();
                    $reduction = ($prixOriginal * $pourcentage) / 100;
                    $prixPromo = $prixOriginal - $reduction;
                    
                    $produit->setPrix($prixPromo);
                    $produit->setPrixOriginal($prixOriginal);
                } else {
                    $produit->setPromotion(false);
                    $produit->setPourcentagePromotion(null);
                }
                
                $produit->setNouveau(true);
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

    #[Route('/vendeur/produit/{id}/modifier', name: 'seller_product_edit')]
    #[IsGranted('ROLE_VENDEUR')]
    public function editProduct(Request $request, Produit $produit, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if ($produit->getVendeur() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier ce produit.');
        }
        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('images')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('products_directory'), $newFilename);
                $produit->setImage($newFilename);
            }
            $em->flush();
            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_seller_products');
        }
        return $this->render('seller/product_edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }

    #[Route('/produit/{id}', name: 'product_show')]
    public function showProduct(Produit $produit): Response
    {
        return $this->render('products/show.html.twig', [
            'produit' => $produit
        ]);
    }
} 
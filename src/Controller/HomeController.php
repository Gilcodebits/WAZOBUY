<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $categories = [
            ['name' => 'Électronique', 'icon' => 'smartphone', 'description' => 'Smartphones, ordinateurs et accessoires'],
            ['name' => 'Mode', 'icon' => 'shirt', 'description' => 'Vêtements, chaussures et accessoires'],
            ['name' => 'Maison', 'icon' => 'home', 'description' => 'Meubles, décoration et électroménager'],
            ['name' => 'Santé & Beauté', 'icon' => 'heart', 'description' => 'Cosmétiques, parfums et soins personnels'],
            ['name' => 'Jeux & Loisirs', 'icon' => 'gamepad', 'description' => 'Jeux vidéo, jouets et activités'],
            ['name' => 'Alimentation', 'icon' => 'utensils', 'description' => 'Produits frais, épicerie et boissons'],
            ['name' => 'Enfants', 'icon' => 'baby', 'description' => 'Vêtements, jouets et accessoires pour enfants'],
            ['name' => 'Autres', 'icon' => 'ellipsis-h', 'description' => 'Découvrez plus de catégories']
        ];

        $popularProducts = [
            [
                'nom' => 'Smartphone Premium',
                'description' => 'Le dernier modèle de smartphone avec les meilleures caractéristiques',
                'prix' => '199 999 FCFA',
                'prixAncien' => '249 999 FCFA',
                'remise' => '20%',
                'note' => 4.5,
                'avis' => 123,
                'isNew' => true,
                'hasDiscount' => false
            ],
            [
                'nom' => 'T-shirt Mode',
                'description' => 'T-shirt en coton biologique, confortable et tendance',
                'prix' => '5 999 FCFA',
                'prixAncien' => null,
                'note' => 4.8,
                'avis' => 87,
                'hasDiscount' => false
            ],
            [
                'nom' => 'Table de Salon',
                'description' => 'Table moderne en bois massif avec finition élégante',
                'prix' => '89 999 FCFA',
                'prixAncien' => '109 999 FCFA',
                'remise' => '20%',
                'note' => 4.6,
                'avis' => 45,
                'hasDiscount' => true
            ]
        ];

        $vendors = [
            [
                'name' => 'Tech Store Bénin',
                'category' => 'Électronique',
                'rating' => 4.8,
                'reviews' => 567,
                'description' => 'Spécialiste des produits électroniques de qualité depuis 2018',
                'verified' => true
            ],
            [
                'name' => 'Mode & Style',
                'category' => 'Mode',
                'rating' => 4.9,
                'reviews' => 345,
                'description' => 'Boutique de mode locale proposant des vêtements de qualité',
                'verified' => true
            ],
            [
                'name' => 'Déco Maison',
                'category' => 'Maison',
                'rating' => 4.7,
                'reviews' => 234,
                'description' => 'Meubles et décoration artisanale made in Bénin',
                'verified' => true
            ]
        ];

        $testimonials = [
            [
                'text' => "Une expérience d'achat exceptionnelle avec des produits de qualité et une livraison rapide.",
                'name' => 'Jean Dupont',
                'location' => 'Cotonou',
                'initials' => 'JD'
            ],
            [
                'text' => "Je suis impressionné par le service client et la variété des produits disponibles.",
                'name' => 'Marie Martin',
                'location' => 'Porto-Novo',
                'initials' => 'MM'
            ],
            [
                'text' => "WAZOBUY a révolutionné mes achats en ligne. Je recommande vivement !",
                'name' => 'Pierre Dubois',
                'location' => 'Abomey',
                'initials' => 'PD'
            ]
        ];

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'popularProducts' => $popularProducts,
            'vendors' => $vendors,
            'testimonials' => $testimonials
        ]);
    }

    #[Route('/vendors', name: 'vendors')]
    public function vendors(): Response
    {
        $vendors = [
            [
                'name' => 'Tech Store Bénin',
                'category' => 'Électronique',
                'rating' => 4.8,
                'reviews' => 567,
                'description' => 'Spécialiste des produits électroniques de qualité depuis 2018',
                'verified' => true
            ],
            [
                'name' => 'Mode & Style',
                'category' => 'Mode',
                'rating' => 4.9,
                'reviews' => 345,
                'description' => 'Boutique de mode locale proposant des vêtements de qualité',
                'verified' => true
            ],
            [
                'name' => 'Déco Maison',
                'category' => 'Maison',
                'rating' => 4.7,
                'reviews' => 234,
                'description' => 'Meubles et décoration artisanale made in Bénin',
                'verified' => true
            ]
        ];

        return $this->render('vendors/index.html.twig', [
            'vendors' => $vendors
        ]);
    }

    #[Route('/produits', name: 'products')]
    public function products(): Response
    {
        $products = [
            [
                'name' => 'Samsung Galaxy A54',
                'category' => 'Électronique',
                'price' => 189000,
                'old_price' => 220000,
                'discount' => 15,
                'rating' => 5,
                'reviews' => 42,
                'description' => 'Smartphone 128Go, 6Go RAM, Noir',
                'image' => 'samsung-a54.jpg',
                'stock' => true
            ],
            [
                'name' => 'Écouteurs Bluetooth Pro',
                'category' => 'Électronique',
                'price' => 45000,
                'rating' => 4,
                'reviews' => 28,
                'description' => 'Sans fil, réduction de bruit active',
                'image' => 'bluetooth-headphones.jpg',
                'stock' => true
            ],
            [
                'name' => 'Montre Classique Homme',
                'category' => 'Accessoires',
                'price' => 35000,
                'rating' => 5,
                'reviews' => 56,
                'description' => 'Bracelet cuir, étanche, quartz',
                'image' => 'classic-watch.jpg',
                'stock' => true
            ],
            [
                'name' => 'Baskets Urban Style',
                'category' => 'Mode',
                'price' => 28000,
                'old_price' => 35000,
                'discount' => 20,
                'rating' => 4,
                'reviews' => 37,
                'description' => 'Confort, respirant, toutes tailles',
                'image' => 'urban-shoes.jpg',
                'stock' => true
            ]
        ];

        return $this->render('products/index.html.twig', [
            'products' => $products
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit *',
                'attr' => [
                    'placeholder' => 'Ex: iPhone 14 Pro Max',
                    'class' => 'wazobuy-input'
                ]
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie *',
                'choices' => [
                    'Électronique' => 'electronics',
                    'Mode & Vêtements' => 'fashion',
                    'Maison & Jardin' => 'home',
                    'Sports & Loisirs' => 'sports',
                    'Beauté & Santé' => 'beauty',
                    'Livres & Médias' => 'books',
                ],
                'placeholder' => 'Sélectionner une catégorie',
                'attr' => [
                    'class' => 'wazobuy-input'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée *',
                'attr' => [
                    'placeholder' => 'Décrivez votre produit en détail...',
                    'class' => 'wazobuy-input'
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (FCFA) *',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'step' => 100,
                    'class' => 'wazobuy-input'
                ]
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock disponible *',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'class' => 'wazobuy-input'
                ]
            ])
            ->add('images', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'multiple' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Formats acceptés: JPG, PNG, WebP',
                    ])
                ],
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'wazobuy-input'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
} 
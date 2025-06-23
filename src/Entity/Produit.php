<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?float $prixOriginal = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\Column]
    private ?bool $nouveau = false;

    #[ORM\Column]
    private ?bool $enPromotion = false;

    #[ORM\Column]
    private ?float $pourcentagePromotion = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private array $caracteristiques = [];

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column]
    private ?int $nombreAvis = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: 'id_vendeur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $vendeur = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCaracteristiques(): array
    {
        return $this->caracteristiques;
    }

    public function setCaracteristiques(array $caracteristiques): self
    {
        $this->caracteristiques = $caracteristiques;
        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getNombreAvis(): ?int
    {
        return $this->nombreAvis;
    }

    public function setNombreAvis(int $nombreAvis): self
    {
        $this->nombreAvis = $nombreAvis;
        return $this;
    }

    public function getPrixOriginal(): ?float
    {
        return $this->prixOriginal;
    }

    public function setPrixOriginal(?float $prixOriginal): self
    {
        $this->prixOriginal = $prixOriginal;
        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function isNouveau(): ?bool
    {
        return $this->nouveau;
    }

    public function setNouveau(bool $nouveau): self
    {
        $this->nouveau = $nouveau;
        return $this;
    }

    public function isEnPromotion(): ?bool
    {
        return $this->enPromotion;
    }

    public function setEnPromotion(bool $enPromotion): self
    {
        $this->enPromotion = $enPromotion;
        return $this;
    }

    public function getPourcentagePromotion(): ?float
    {
        return $this->pourcentagePromotion;
    }

    public function setPourcentagePromotion(?float $pourcentagePromotion): self
    {
        $this->pourcentagePromotion = $pourcentagePromotion;
        return $this;
    }

    public function getVendeur(): ?Utilisateur
    {
        return $this->vendeur;
    }

    public function setVendeur(?Utilisateur $vendeur): self
    {
        $this->vendeur = $vendeur;
        return $this;
    }
}

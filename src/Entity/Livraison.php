<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = 'en_attente';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statutLivreur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_livreur', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $livreur = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateAcceptation = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateLivraisonReelle = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $localisation = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $instructionsLivraison = null;

    #[ORM\Column]
    private ?float $fraisLivraison = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'livraisons')]
    #[ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande', nullable: false)]
    private ?Commande $commande = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->dateLivraison = new \DateTimeImmutable();
        $this->codeLivraison = $this->generateCodeLivraison();
    }

    private function generateCodeLivraison(): string
    {
        return 'LVR-' . date('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateLivraison(): ?\DateTimeImmutable
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(\DateTimeImmutable $dateLivraison): static
    {
        $this->dateLivraison = $dateLivraison;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getStatutLivreur(): ?string
    {
        return $this->statutLivreur;
    }

    public function setStatutLivreur(?string $statutLivreur): static
    {
        $this->statutLivreur = $statutLivreur;
        return $this;
    }

    public function getLivreur(): ?Utilisateur
    {
        return $this->livreur;
    }

    public function setLivreur(?Utilisateur $livreur): static
    {
        $this->livreur = $livreur;
        return $this;
    }

    public function getDateAcceptation(): ?\DateTimeImmutable
    {
        return $this->dateAcceptation;
    }

    public function setDateAcceptation(\DateTimeImmutable $dateAcceptation): static
    {
        $this->dateAcceptation = $dateAcceptation;
        return $this;
    }

    public function getDateLivraisonReelle(): ?\DateTimeImmutable
    {
        return $this->dateLivraisonReelle;
    }

    public function setDateLivraisonReelle(\DateTimeImmutable $dateLivraisonReelle): static
    {
        $this->dateLivraisonReelle = $dateLivraisonReelle;
        return $this;
    }

    public function getLocalisation(): array
    {
        return $this->localisation;
    }

    public function setLocalisation(array $localisation): static
    {
        $this->localisation = $localisation;
        return $this;
    }

    public function getCodeLivraison(): ?string
    {
        return $this->codeLivraison;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;
        return $this;
    }

    public function getInstructionsLivraison(): ?string
    {
        return $this->instructionsLivraison;
    }

    public function setInstructionsLivraison(?string $instructionsLivraison): static
    {
        $this->instructionsLivraison = $instructionsLivraison;
        return $this;
    }

    public function getFraisLivraison(): ?float
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(float $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;
        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }
}

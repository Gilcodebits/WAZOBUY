<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commandes')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id_commande', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateCommande;

    #[ORM\Column(type: Types::FLOAT)]
    private float $montantTotal;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $statut;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur')]
    private ?Utilisateur $client = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ventes')]
    #[ORM\JoinColumn(name: 'id_vendeur', referencedColumnName: 'id_utilisateur')]
    private ?Utilisateur $vendeur = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Livraison::class)]
    private Collection $livraisons;

    public function __construct()
    {
        $this->dateCommande = new \DateTimeImmutable();
        $this->statut = 'en_attente';
        $this->livraisons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): \DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function getMontantTotal(): float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): self
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getClient(): ?Utilisateur
    {
        return $this->client;
    }

    public function setClient(?Utilisateur $client): self
    {
        $this->client = $client;
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

    public function getLivraisons(): Collection
    {
        return $this->livraisons;
    }
}

<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::FLOAT)]
    private float $montant;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $datePaiement;

    #[ORM\Column(type: Types::STRING, length: 30)]
    private string $statut;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $client = null;

    public function __construct()
    {
        $this->datePaiement = new \DateTimeImmutable();
        $this->statut = 'en_attente';
    }

    public function getId(): ?int { return $this->id; }
    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }
    public function getDatePaiement(): \DateTimeImmutable { return $this->datePaiement; }
    public function setDatePaiement(\DateTimeImmutable $date): self { $this->datePaiement = $date; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function getClient(): ?Utilisateur { return $this->client; }
    public function setClient(?Utilisateur $client): self { $this->client = $client; return $this; }
} 
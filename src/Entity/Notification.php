<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $titre;

    #[ORM\Column(type: 'text')]
    private string $message;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'destinataire_id', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $destinataire = null;

    #[ORM\Column(type: 'boolean')]
    private bool $estLue = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'auteur_id', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $auteur = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }
    public function getDestinataire(): ?Utilisateur { return $this->destinataire; }
    public function setDestinataire(?Utilisateur $destinataire): self { $this->destinataire = $destinataire; return $this; }
    public function isEstLue(): bool { return $this->estLue; }
    public function setEstLue(bool $estLue): self { $this->estLue = $estLue; return $this; }
    public function getDateCreation(): \DateTimeImmutable { return $this->dateCreation; }
    public function setDateCreation(\DateTimeImmutable $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }
    public function getLien(): ?string { return $this->lien; }
    public function setLien(?string $lien): self { $this->lien = $lien; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): self { $this->type = $type; return $this; }
    public function getAuteur(): ?Utilisateur { return $this->auteur; }
    public function setAuteur(?Utilisateur $auteur): self { $this->auteur = $auteur; return $this; }
} 
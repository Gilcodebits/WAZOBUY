<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $expediteur;

    #[ORM\Column(type: 'string', length: 255)]
    private string $destinataire;

    #[ORM\Column(type: 'text')]
    private string $contenu;

    #[ORM\Column(type: 'boolean')]
    private bool $estLu = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $dateEnvoi;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $conversationId = null;

    public function getId(): ?int { return $this->id; }
    public function getExpediteur(): string { return $this->expediteur; }
    public function setExpediteur(string $expediteur): self { $this->expediteur = $expediteur; return $this; }
    public function getDestinataire(): string { return $this->destinataire; }
    public function setDestinataire(string $destinataire): self { $this->destinataire = $destinataire; return $this; }
    public function getContenu(): string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }
    public function isEstLu(): bool { return $this->estLu; }
    public function setEstLu(bool $estLu): self { $this->estLu = $estLu; return $this; }
    public function getDateEnvoi(): \DateTimeImmutable { return $this->dateEnvoi; }
    public function setDateEnvoi(\DateTimeImmutable $dateEnvoi): self { $this->dateEnvoi = $dateEnvoi; return $this; }
    public function getConversationId(): ?string { return $this->conversationId; }
    public function setConversationId(?string $conversationId): self { $this->conversationId = $conversationId; return $this; }
} 
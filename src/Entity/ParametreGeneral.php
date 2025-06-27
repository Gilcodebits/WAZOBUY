<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ParametreGeneral
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(length: 20)]
    private $theme = 'auto';

    #[ORM\Column(length: 5)]
    private $langue = 'fr';

    #[ORM\Column(length: 40)]
    private $fuseau = 'Africa/Porto-Novo';

    #[ORM\Column(type: 'integer')]
    private $elementsParPage = 10;

    // Paramètres de notifications
    #[ORM\Column(type: 'boolean')]
    private $notificationsEmail = false;

    #[ORM\Column(type: 'boolean')]
    private $notifCommandes = true;

    #[ORM\Column(type: 'boolean')]
    private $notifLitiges = true;

    #[ORM\Column(type: 'boolean')]
    private $notifVendeurs = true;

    #[ORM\Column(type: 'boolean')]
    private $notifMessages = true;

    #[ORM\Column(length: 20)]
    private $notifRapport = 'quotidien';

    // Paramètres d'apparence
    #[ORM\Column(type: 'boolean')]
    private $animations = true;

    // Paramètres de sécurité
    #[ORM\Column(type: 'boolean')]
    private $deuxFacteurs = false;

    #[ORM\Column(type: 'boolean')]
    private $notifConnexion = true;

    #[ORM\Column(type: 'integer')]
    private $sessionExp = 30;

    public function getId(): ?int { return $this->id; }
    public function getTheme(): ?string { return $this->theme; }
    public function setTheme(string $theme): self { $this->theme = $theme; return $this; }
    public function getLangue(): ?string { return $this->langue; }
    public function setLangue(string $langue): self { $this->langue = $langue; return $this; }
    public function getFuseau(): ?string { return $this->fuseau; }
    public function setFuseau(string $fuseau): self { $this->fuseau = $fuseau; return $this; }
    public function getElementsParPage(): ?int { return $this->elementsParPage; }
    public function setElementsParPage(int $nb): self { $this->elementsParPage = $nb; return $this; }

    // Getters et setters pour les notifications
    public function getNotificationsEmail(): ?bool { return $this->notificationsEmail; }
    public function setNotificationsEmail(bool $notificationsEmail): self { $this->notificationsEmail = $notificationsEmail; return $this; }

    public function getNotifCommandes(): ?bool { return $this->notifCommandes; }
    public function setNotifCommandes(bool $notifCommandes): self { $this->notifCommandes = $notifCommandes; return $this; }

    public function getNotifLitiges(): ?bool { return $this->notifLitiges; }
    public function setNotifLitiges(bool $notifLitiges): self { $this->notifLitiges = $notifLitiges; return $this; }

    public function getNotifVendeurs(): ?bool { return $this->notifVendeurs; }
    public function setNotifVendeurs(bool $notifVendeurs): self { $this->notifVendeurs = $notifVendeurs; return $this; }

    public function getNotifMessages(): ?bool { return $this->notifMessages; }
    public function setNotifMessages(bool $notifMessages): self { $this->notifMessages = $notifMessages; return $this; }

    public function getNotifRapport(): ?string { return $this->notifRapport; }
    public function setNotifRapport(string $notifRapport): self { $this->notifRapport = $notifRapport; return $this; }

    // Getters et setters pour l'apparence
    public function getAnimations(): ?bool { return $this->animations; }
    public function setAnimations(bool $animations): self { $this->animations = $animations; return $this; }

    // Getters et setters pour la sécurité
    public function getDeuxFacteurs(): ?bool { return $this->deuxFacteurs; }
    public function setDeuxFacteurs(bool $deuxFacteurs): self { $this->deuxFacteurs = $deuxFacteurs; return $this; }

    public function getNotifConnexion(): ?bool { return $this->notifConnexion; }
    public function setNotifConnexion(bool $notifConnexion): self { $this->notifConnexion = $notifConnexion; return $this; }

    public function getSessionExp(): ?int { return $this->sessionExp; }
    public function setSessionExp(int $sessionExp): self { $this->sessionExp = $sessionExp; return $this; }
} 
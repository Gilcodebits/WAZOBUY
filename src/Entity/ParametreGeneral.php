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

    // ... autres paramètres globaux (notifications, etc.)

    public function getId(): ?int { return $this->id; }
    public function getTheme(): ?string { return $this->theme; }
    public function setTheme(string $theme): self { $this->theme = $theme; return $this; }
    public function getLangue(): ?string { return $this->langue; }
    public function setLangue(string $langue): self { $this->langue = $langue; return $this; }
    public function getFuseau(): ?string { return $this->fuseau; }
    public function setFuseau(string $fuseau): self { $this->fuseau = $fuseau; return $this; }
    public function getElementsParPage(): ?int { return $this->elementsParPage; }
    public function setElementsParPage(int $nb): self { $this->elementsParPage = $nb; return $this; }
} 
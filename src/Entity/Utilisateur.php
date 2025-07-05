<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id_utilisateur', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, max: 50)]
    private string $nom;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true, nullable: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email n'est pas valide")]
    private ?string $email = null;

    #[ORM\Column(name: 'mot_de_passe', type: Types::STRING, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(min: 8, max: 20)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'email_verifie', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $emailVerifie = false;

    #[ORM\Column(name: 'photo_profil', type: Types::STRING, length: 255, nullable: true)]
    private ?string $photoProfil = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'est_actif', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $estActif = true;

    #[ORM\Column(name: 'derniere_connexion', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $derniereConnexion = null;

    #[ORM\Column(name: 'agree_terms', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $agreeTerms = false;

    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 6, max: 4096)]
    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $socialId = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Adresse::class)]
    private Collection $adresses;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'vendeur', targetEntity: Commande::class)]
    private Collection $ventes;

    #[ORM\OneToMany(mappedBy: 'vendeur', targetEntity: Produit::class)]
    private Collection $produits;

    #[ORM\ManyToMany(targetEntity: Produit::class)]
    #[ORM\JoinTable(
        name: 'utilisateur_favoris',
        joinColumns: [
            new ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id_utilisateur')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'produit_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $favoris;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
        $this->adresses = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->ventes = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function isEmailVerifie(): ?bool
    {
        return $this->emailVerifie;
    }

    public function setEmailVerifie(bool $emailVerifie): static
    {
        $this->emailVerifie = $emailVerifie;
        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function isEstActif(): bool
    {
        return $this->estActif;
    }

    public function setEstActif(bool $estActif): static
    {
        $this->estActif = $estActif;
        return $this;
    }

    public function setDerniereConnexion(?\DateTimeImmutable $derniereConnexion): static
    {
        $this->derniereConnexion = $derniereConnexion;
        return $this;
    }

    public function isAgreeTerms(): bool
    {
        return $this->agreeTerms;
    }

    public function setAgreeTerms(bool $agreeTerms): static
    {
        $this->agreeTerms = $agreeTerms;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    // Méthode pour afficher le nom complet
    public function getNomComplet(): string
    {
        return $this->prenom.' '.$this->nom;
    }

    public function getSocialId(): ?string
    {
        return $this->socialId;
    }

    public function setSocialId(?string $socialId): static
    {
        $this->socialId = $socialId;
        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeImmutable
    {
        return $this->derniereConnexion;
    }

    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function getVentes(): Collection
    {
        return $this->ventes;
    }

    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Produit $produit): static
    {
        if (!$this->favoris->contains($produit)) {
            $this->favoris[] = $produit;
        }
        return $this;
    }

    public function removeFavori(Produit $produit): static
    {
        $this->favoris->removeElement($produit);
        return $this;
    }
}
?>
<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParticipantRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Email déjà pris')]
#[UniqueEntity(fields: ['pseudo'], message: 'Pseudo déjà pris')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Email obligatoire')]
    #[Assert\Email(message: 'Email invalide')]
    private ?string $email = null;

    //supprimer roles
    // #[ORM\Column]
    // private array $roles = [];

    /**
     * @var string The hashed password
     */
    
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Mot de passe obligatoire')]
    #[Assert\Length(min: 8, max: 4000, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères', maxMessage: 'Le mot de passe doit faire au plus {{ limit }} caractères')]
    private ?string $motPasse = null;


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Nom obligatoire')]
    #[Assert\Length(min: 3, max: 50, minMessage: 'Le nom doit faire au moins 3 caractères', maxMessage: 'Le nom doit faire au plus 50 caractères')]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Prénom obligatoire')]
    #[Assert\Length(min: 3, max: 50, minMessage: 'Le prénom doit faire au moins 3 caractères', maxMessage: 'Le prénom doit faire au plus 50 caractères')]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Pseudo obligatoire')]
    #[Assert\Length(min: 3, max: 50, minMessage: 'Le pseudo doit faire au moins 3 caractères', maxMessage: 'Le pseudo doit faire au plus 50 caractères')]
    private ?string $pseudo = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(pattern: '/^0[1-9]([-. ]?[0-9]{2}){4}$/')]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $administrateur = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'inscrits')]
    private Collection $sortiesInscrites;

    public function __construct()
    {
        // $this->roles = ['ROLE_USER'];
        $this->administrateur = false;
        $this->actif = true;
        $this->sortiesOrganisees = new ArrayCollection();
        $this->sortiesInscrites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    //définir les roles en fonction de administrateur
    public function getRoles(): array
    {
        $roles = ['ROLE_USER']; // Par défaut, tous les participants ont le rôle utilisateur

        if ($this->administrateur) {
            $roles[] = 'ROLE_ADMIN'; // Ajouter le rôle admin si l'utilisateur est administrateur
        }

        return $roles;
    }
    
    /**
     * @see PasswordAuthenticatedUserInterface
     */

    //changer en mot de passe
    public function getPassword(): string
    {
        return $this->motPasse;
    }
        
    public function getMotPasse(): string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): static
    {
        $this->motPasse = $motPasse;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

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

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscrites(): Collection
    {
        return $this->sortiesInscrites;
    }

    public function addSortiesInscrite(Sortie $sortiesInscrite): static
    {
        if (!$this->sortiesInscrites->contains($sortiesInscrite)) {
            $this->sortiesInscrites->add($sortiesInscrite);
            $sortiesInscrite->addInscrit($this);
        }

        return $this;
    }

    public function removeSortiesInscrite(Sortie $sortiesInscrite): static
    {
        if ($this->sortiesInscrites->removeElement($sortiesInscrite)) {
            $sortiesInscrite->removeInscrit($this);
        }

        return $this;
    }
}

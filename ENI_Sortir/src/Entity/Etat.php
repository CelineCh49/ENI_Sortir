<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
#[UniqueEntity(fields: ['libelle'], message: 'Libellé d\'état déjà créé')]
class Etat
{
    const CREEE = 'Créée';
    const OUVERTE = 'Ouverte';
    const CLOTUREE = 'Clôturée';
    const ACTIVITEE_EN_COURS = 'Activité en cours';
    const PASSEE = 'Passée';
    const ANNULEE = 'Annulée';
    const HISTORISEE = 'Historisée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'etat', targetEntity: Sortie::class)]
    private Collection $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        if(!in_array($libelle, [self::CREEE, self::OUVERTE, self::CLOTUREE, self::ACTIVITEE_EN_COURS, self::PASSEE, self::ANNULEE, self::HISTORISEE])){
            throw new \InvalidArgumentException("Etat non valide");
        }
        $this->libelle = $libelle;

        return $this;
    }


    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->setEtat($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getEtat() === $this) {
                $sorty->setEtat(null);
            }
        }

        return $this;
    }
}

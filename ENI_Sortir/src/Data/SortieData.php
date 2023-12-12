<?php

namespace App\Data;

use DateTime;

class SortieData
{
    private ?string $nom = null;
    private ?DateTime $dateHeureDebut = null;
    private ?int $duree = null;
    private ?DateTime $dateLimiteInscription = null;
    private ?int $nbInscriptionsMax = null;
    private ?string $infosSortie = null;
    private ?int $lieuId = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDateHeureDebut(): ?DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?DateTime $dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): void
    {
        $this->duree = $duree;
    }

    public function getDateLimiteInscription(): ?DateTime
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?DateTime $dateLimiteInscription): void
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(?int $nbInscriptionsMax): void
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): void
    {
        $this->infosSortie = $infosSortie;
    }

    public function getLieuId(): ?int
    {
        return $this->lieuId;
    }

    public function setLieuId(?int $lieuId): void
    {
        $this->lieuId = $lieuId;
    }
}
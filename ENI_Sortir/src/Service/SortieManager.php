<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManager;

class SortieManager 
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EtatRepository $etatRepository
    ) {}

    public function handleInscription(Sortie $sortie, Participant $participant): void 
    {
        $sortie->addInscrit($participant);

        if (count($sortie->getInscrits()) == $sortie->getNbInscriptionsMax()) {
            $etatCloture = $etatRepository->findbyLibelle('Clôturée');
            $sortie->setEtat($etatCloture);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    public function handleDesistement(Sortie $sortie, Participant $participant): void 
    {
        $sortie->removeInscrit($participant);

        if (count($sortie->getInscrits()) < $sortie->getNbInscriptionsMax() && $sortie->getDateLimiteInscription() >= new DateTime('now')) {
            $etatOuvert = $etatRepository->findbyLibelle('Ouverte');
            $sortie->setEtat($etatOuvert);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    public function handleAnnulation(Sortie $sortie): void 
    {
        $etatAnnulee = $etatRepository->findbyLibelle('Annulée');
        $sortie->setEtat($etatAnnulee);

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    

    public function handleModification(Sortie $sortie): void 
    {
        if (count($sortie->getInscrits()) == $sortie->getNbInscriptionsMax()) {
            $etatCloture = $etatRepository->findbyLibelle('Clôturée');
            $sortie->setEtat($etatCloture);
        } else {
            $etatOuvert = $etatRepository->findbyLibelle('Ouverte');
            $sortie->setEtat($etatOuvert);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    public function handleCloture(Sortie $sortie): void 
    {
        $etatCloture = $etatRepository->findbyLibelle('Clôturée');
        $sortie->setEtat($etatCloture);

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    public function handleArchivage(Sortie $sortie): void 
    {
        $etatArchivee = $etatRepository->findbyLibelle('Archivée');
        $sortie->setEtat($etatArchivee);

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    public function handleSuppression(Sortie $sortie): void 
    {
        $entityManager->remove($sortie);
        $entityManager->flush();
    }

    public function handleRecherche(): void 
    {
        $sorties = $sortieRepository->findAll();

        foreach ($sorties as $sortie) {
            if ($sortie->getDateHeureDebut() < new DateTime('now') && $sortie->getEtat()->getLibelle() == 'Ouverte') {
                $etatCloture = $etatRepository->findbyLibelle('Clôturée');
                $sortie->setEtat($etatCloture);
            } elseif ($sortie->getDateHeureDebut() < new DateTime('now') && $sortie->getEtat()->getLibelle() == 'Clôturée') {
                $etatArchivee = $etatRepository->findbyLibelle('Archivée');
                $sortie->setEtat($etatArchivee);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();
        }
    }
    public function handlePublication(Sortie $sortie, EtatRepository $etatRepository, EntityManager $entityManager): void 
    {
        $etatCréée = $etatRepository->findbyLibelle('Créée');

        if ($sortie->getEtat() == $etatCréée) {
            $etatOuverte = $etatRepository->findbyLibelle('Ouverte');
            $sortie->setEtat($etatOuverte);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();
    }

}

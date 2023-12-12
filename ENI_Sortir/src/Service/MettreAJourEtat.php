<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use DateTime;

class MettreAJourEtat
{
    private $logger;

    // Injection de dépendances du service.
    public function __construct(
        private SortieRepository $sortieRepository,
        private EtatRepository $etatRepository,
        private EntityManagerInterface $entityManager,
        LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function gererEtats(): int
    {
        $compteur = 0;
        $now = new DateTime();
        $dateNowMoinsUnMois = new DateTime("-1 month");

        // Récupération anticipée des états pour éviter des appels répétés au repository.
        $etatOuverte = $this->etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $etatCloturee = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
        $etatEnCours = $this->etatRepository->findOneBy(['libelle' => 'Activité en cours']);
        $etatPassee = $this->etatRepository->findOneBy(['libelle' => 'Passée']);
        $etatArchivee = $this->etatRepository->findOneBy(['libelle' => 'Historisée']);

        if (!$etatOuverte || !$etatCloturee || !$etatEnCours || !$etatPassee || !$etatArchivee) {
            throw new \Exception("Certains états sont manquants dans la base de données.");
        }

        $this->entityManager->beginTransaction();
        
        try {
            $sorties = $this->sortieRepository->findAll();

            foreach ($sorties as $sortie) {
                $etatActuel = $sortie->getEtat();

                if ($etatActuel == $etatOuverte && $now >= $sortie->getDateLimiteInscription()) {
                    $sortie->setEtat($etatCloturee);
                    $compteur++;
                } elseif ($etatActuel == $etatCloturee && $sortie->getDateHeureDebut() <= $now) {
                    $sortie->setEtat($etatEnCours);
                    $compteur++;
                } elseif ($etatActuel == $etatEnCours && $sortie->getDateFin() <= $now) {
                    $sortie->setEtat($etatPassee);
                    $compteur++;
                } elseif ($sortie->getDateFin() < $dateNowMoinsUnMois && $etatActuel != $etatArchivee) {
                    $sortie->setEtat($etatArchivee);
                    $compteur++;
                }
                $this->entityManager->persist($sortie);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Erreur lors de la mise à jour des états des sorties: ' . $e->getMessage());
            throw $e;
        }

        $this->logger->info($compteur . ' sorties ont été mises à jour.');
        return $compteur;
    }
}

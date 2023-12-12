<?php

namespace App\Command;

use App\Service\MettreAJourEtat;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MettreAJourEtatsCommand extends Command
{
    protected static $defaultName = 'update:sortie:etat';

    private $serviceEtatSortie;

    public function __construct(MettreAJourEtat $serviceEtatSortie)
    {
        parent::__construct();
        $this->serviceEtatSortie = $serviceEtatSortie;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nombreSortiesMisesAJour = $this->serviceEtatSortie->gererEtats();

        $output->writeln($nombreSortiesMisesAJour . ' sorties ont été mises à jour.');

        return Command::SUCCESS;
    }
}


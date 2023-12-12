<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findNbInscrits(Sortie $sortie): int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(p.id)')
            ->join('s.inscrits', 'p') // "inscrits" is the property name in the Sortie entity
            ->where('s.id = :sortieId')
            ->setParameter('sortieId', $sortie->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findSortieParDateEtEtat(\DateTime $date, string $etatLibelle): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.etat', 'e')
            ->where('e.libelle = :etatLibelle')
            ->andWhere('s.dateHeureDebut = :date')
            ->setParameter('etatLibelle', $etatLibelle)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Récupère les sorties selon un état spécifique.
     *
     * @param string $etatLeLibelle L'état à rechercher (par exemple, 'Ouverte', 'Clôturée', etc.)
     * @return Sortie[] Un tableau des sorties correspondant à l'état spécifié.
     */
    public function findSortiesParEtat(string $etatLeLibelle): array
    {
        $qb = $this->createQueryBuilder('s');
        
        // Jointure avec l'entité Etat pour filtrer par état.
        $qb->innerJoin('s.etat', 'e')
           ->where('e.libelle = :etat')
           ->setParameter('etat', $etatLeLibelle);
           
        return $qb->getQuery()->getResult();
    }


    
    //Méthode pour rechercher des sorties basées sur des critères spécifiques
    public function findSearch(SearchData $search, Participant $participant )
    { 
        $now = new \Datetime();

        $query=$this
            ->createQueryBuilder('s')
            ->select ('s','e','c','i','o')
            ->join('s.campus', 'c')
            ->leftjoin('s.inscrits', 'i')
            ->join('s.etat', 'e')
            ->leftJoin('s.organisateur', 'o');

            
        //filtre Campus
        if (isset ($search->campus))
        {
            $query=$query
            -> andWhere('s.campus = :campus')
            -> setParameter('campus', $search->campus);
        }

        // filtre champ de recherche       
        if(!empty($search))
        {
            $query=$query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        // filtre dateMin
        if (isset ($search->dateMin))
        {
            $query=$query
                -> andWhere('s.dateHeureDebut >= :dateMin')
                -> setParameter('dateMin', $search->dateMin);
        }

        // filtre dateMax
        if (isset ($search->dateMax))
        {
            $query=$query
                -> andWhere('s.dateHeureDebut <= :dateMax')
                -> setParameter('dateMax', $search->dateMax);
        }

        // filtre sortiesOrganisees
        if ($search->sortiesOrganisees)
        {
            $query=$query
                -> andWhere('s.organisateur = :organisateur')
                -> setParameter('organisateur', $participant);
        }

        // filtre sortiesInscrit
        if ($search->sortiesInscrit)
        {
            $query=$query
                ->andWhere( ':participant MEMBER OF s.inscrits')
                ->setParameter('participant', $participant);
        }

        // filtre sortiesPasInscrit
        if ($search->sortiesPasInscrit) 
        {
            $query = $query
                ->andWhere( ':participant  not MEMBER OF s.inscrits')
                ->setParameter('participant', $participant);
        }

        // filtre sortiesPassées    --------a faire
        if ($search->sortiesPassee) 
        {
            $query = $query
                -> andWhere('s.dateHeureDebut < :dateNow')
                -> setParameter('dateNow', $now );
        }

        return $query->getQuery()->getResult();
    }
}

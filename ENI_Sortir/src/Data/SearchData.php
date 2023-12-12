<?php

namespace App\Data;

use App\Entity\Campus;
use App\Entity\Sortie;
use DateTime;

class SearchData
{
    public ?Campus $campus =null;
    public ?string $q = null;
    public ?DateTime $dateMin=null;
    public ?Datetime $dateMax= null;
    public bool $sortiesOrganisees = false;
    public bool $sortiesInscrit= false;
    public bool $sortiesPasInscrit =false;
    public bool $sortiesPassee = false;
     
}



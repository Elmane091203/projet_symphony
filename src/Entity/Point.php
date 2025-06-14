<?php

namespace App\Entity;

use App\Repository\PointRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity(repositoryClass: PointRepository::class)]
class Point
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(type:"string", length:255)]
    private string $nom;
    
    #[ManyToOne(targetEntity: Zone::class,cascade: ['persist'], inversedBy: 'points')]
    #[JoinColumn(name: 'zone_id', referencedColumnName: 'id')]
    private ?Zone $zone ; 
    
    #[ORM\Column(type: "integer")]
    private int $nb_habitants;
    #[ORM\Column(type: "integer")]
    private int $nb_symptomatiques;
    #[ORM\Column(type: "integer")]
    private int $nb_positifs;

    public function __construct() {
    }

    
    public function getId(): int {
        return $this->id;
    }
    
    public function getNom(): string {
        return $this->nom;
    }
    
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function getZone(): Zone {
        return $this->zone;
    }
    
    public function setZone(?Zone $zone): void {
        $this->zone = $zone;
    }
    
    public function getNbHabitants(): int
    {
        return $this->nb_habitants;
    }


    public function setNbHabitants(int $nb_habitants): void
    {
        $this->nb_habitants = $nb_habitants;
        if ($this->zone) {
            $this->zone->updateStatut();
        }
    }

    public function getNbSymptomatiques(): int
    {
        return $this->nb_symptomatiques;
    }

    public function setNbSymptomatiques(int $nb_symptomatiques): void
    {
        $this->nb_symptomatiques = $nb_symptomatiques;
        if ($this->zone) {
            $this->zone->updateStatut();
        }
    }

    public function getNbPositifs(): int
    {
        return $this->nb_positifs;
    }

    public function setNbPositifs(int $nb_positifs): void
    {
        $this->nb_positifs = $nb_positifs;
        if ($this->zone) {
            $this->zone->updateStatut();
        }
    }
}

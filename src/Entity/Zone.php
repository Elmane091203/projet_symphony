<?php

namespace App\Entity;

use App\Repository\ZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
class Zone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = 'verte';

    #[ManyToOne(targetEntity: Pays::class, inversedBy: 'zones')]
    #[JoinColumn(name: 'pays_id', referencedColumnName: 'id')]
    private Pays $pays;

    #[ORM\OneToMany(targetEntity: Point::class, cascade: ['persist', 'remove'], mappedBy: "zone")]
    private Collection $points;

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPays(): Pays
    {
        return $this->pays;
    }

    public function setPays(Pays $pays): void
    {
        $this->pays = $pays;
    }

    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): void
    {
        if (!$this->points->contains($point)) {
            $point->setZone($this);
            $this->points->add($point);
            $this->updateStatut();
        }
    }

    public function removePoint(Point $point): void
    {
        if ($this->points->removeElement($point)) {
            if ($point->getZone() === $this) {
                $point->setZone(null);
            }
            $this->updateStatut();
        }
    }

    public function updateStatut(): void
    {
        $totalHabitants = 0;
        $totalPositifs = 0;

        foreach ($this->points as $point) {
            $totalHabitants += $point->getNbHabitants();
            $totalPositifs += $point->getNbPositifs();
        }

        if ($totalHabitants === 0) {
            $this->statut = 'vert';
            return;
        }

        $pourcentagePositifs = ($totalPositifs / $totalHabitants) * 100;

        if ($pourcentagePositifs < 5) {
            $this->statut = 'vert';
        } elseif ($pourcentagePositifs <= 15) {
            $this->statut = 'orange';
        } else {
            $this->statut = 'rouge';
        }
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function updateFrom(Zone $zone): void
    {
        $this->nom = $zone->getNom();
        $this->statut = $zone->getStatut();

    }
    
}

<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column]
    private ?int $raceTime = null;

    #[ORM\Column]
    private ?int $placement = null;

    #[ORM\Column(length: 255)]
    private ?string $distance = null;

    #[ORM\ManyToOne(inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Race $race = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getRaceTime(): ?int
    {
        return $this->raceTime;
    }

    public function setRaceTime(int $raceTime): self
    {
        $this->raceTime = $raceTime;

        return $this;
    }

    public function getPlacement(): ?int
    {
        return $this->placement;
    }

    public function setPlacement(int $placement): self
    {
        $this->placement = $placement;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }
}

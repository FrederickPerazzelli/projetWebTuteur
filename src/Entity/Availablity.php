<?php

namespace App\Entity;

use App\Repository\AvailablityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailablityRepository::class)]
class Availablity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: WeekDay::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $day;

    #[ORM\Column(type: 'time')]
    private $beginTime;

    #[ORM\Column(type: 'time')]
    private $endTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?WeekDay
    {
        return $this->day;
    }

    public function setDay(?WeekDay $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getBeginTime(): ?\DateTimeInterface
    {
        return $this->beginTime;
    }

    public function setBeginTime(\DateTimeInterface $beginTime): self
    {
        $this->beginTime = $beginTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }
}

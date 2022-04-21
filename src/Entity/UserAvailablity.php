<?php

namespace App\Entity;

use App\Repository\UserAvailablityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAvailablityRepository::class)]
class UserAvailablity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Availablity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $availablity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->User = $user;

        return $this;
    }

    public function getAvailablity(): ?Availablity
    {
        return $this->availablity;
    }

    public function setAvailablity(?Availablity $availablity): self
    {
        $this->availablity = $availablity;

        return $this;
    }
}

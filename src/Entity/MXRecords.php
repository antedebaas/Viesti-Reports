<?php

namespace App\Entity;

use App\Repository\MXRecordsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MXRecordsRepository::class)]
class MXRecords
{
    public function __toString() {
        return $this->name;
    }
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'MXRecords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domains $domain = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDomain(): ?Domains
    {
        return $this->domain;
    }

    public function setDomain(?Domains $domain): static
    {
        $this->domain = $domain;

        return $this;
    }
}

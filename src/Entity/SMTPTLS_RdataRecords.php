<?php

namespace App\Entity;

use App\Repository\SMTPTLS_RdataRecordsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SMTPTLS_RdataRecordsRepository::class)]
class SMTPTLS_RdataRecords
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'SMTPTLS_RdataRecords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SMTPTLS_Policies $policy = null;

    #[ORM\Column]
    private ?int $usagetype = null;

    #[ORM\Column]
    private ?int $selectortype = null;

    #[ORM\Column]
    private ?int $matchingtype = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPolicy(): ?SMTPTLS_Policies
    {
        return $this->policy;
    }

    public function setPolicy(?SMTPTLS_Policies $policy): static
    {
        $this->policy = $policy;

        return $this;
    }

    public function getUsagetype(): ?int
    {
        return $this->usagetype;
    }

    public function setUsagetype(int $usagetype): static
    {
        $this->usagetype = $usagetype;

        return $this;
    }

    public function getSelectortype(): ?int
    {
        return $this->selectortype;
    }

    public function setSelectortype(int $selectortype): static
    {
        $this->selectortype = $selectortype;

        return $this;
    }

    public function getMatchingtype(): ?int
    {
        return $this->matchingtype;
    }

    public function setMatchingtype(int $matchingtype): static
    {
        $this->matchingtype = $matchingtype;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }
}

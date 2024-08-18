<?php

namespace App\Entity;

use App\Repository\DMARC_ResultsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DMARC_ResultsRepository::class)]
class DMARC_Results
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DMARC_Records $record = null;

    #[ORM\Column(length: 255)]
    private ?string $domain = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $result = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dkim_selector = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecord(): ?DMARC_Records
    {
        return $this->record;
    }

    public function setRecord(?DMARC_Records $record): static
    {
        $this->record = $record;

        return $this;
    }


    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getDkimSelector(): ?string
    {
        return $this->dkim_selector;
    }

    public function setDkimSelector(string $dkim_selector): static
    {
        $this->dkim_selector = $dkim_selector;

        return $this;
    }
}

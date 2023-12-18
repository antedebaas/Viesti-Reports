<?php

namespace App\Entity;

use App\Repository\SMTPTLSMXRecordsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SMTPTLSMXRecordsRepository::class)]
class SMTPTLS_MXRecords
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?MXRecords $MXRecord = null;

    #[ORM\ManyToOne(inversedBy: 'SMTPTLS_MXRecords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SMTPTLS_Policies $policy = null;

    #[ORM\Column]
    private ?int $priority = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMXRecord(): ?MXRecords
    {
        return $this->MXRecord;
    }

    public function setMXRecord(?MXRecords $MXRecord): static
    {
        $this->MXRecord = $MXRecord;

        return $this;
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

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\SMTPTLS_SeenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SMTPTLS_SeenRepository::class)]
class SMTPTLS_Seen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SMTPTLS_Reports $report = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReport(): ?SMTPTLS_Reports
    {
        return $this->report;
    }

    public function setReport(?SMTPTLS_Reports $report): static
    {
        $this->report = $report;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }
}

<?php

namespace App\EntityUnmanaged;

use App\Repository\DoctrineMigrationVersionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineMigrationVersionsRepository::class)]
class DoctrineMigrationVersions
{
    #[ORM\Id]
    #[ORM\Column(length: 191)]
    private ?string $version = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $executed_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $execution_time = null;

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getExecutedAt(): ?\DateTimeInterface
    {
        return $this->executed_at;
    }

    public function setExecutedAt(?\DateTimeInterface $executed_at): static
    {
        $this->executed_at = $executed_at;

        return $this;
    }

    public function getExecutionTime(): ?int
    {
        return $this->execution_time;
    }

    public function setExecutionTime(?int $execution_time): static
    {
        $this->execution_time = $execution_time;

        return $this;
    }
}

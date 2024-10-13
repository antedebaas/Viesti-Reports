<?php

namespace App\Entity;

use App\Repository\LogsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Enums\StateType;

#[ORM\Entity(repositoryClass: LogsRepository::class)]
class Logs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $time;

    #[ORM\Column(type: Types::TEXT)]
    private string $message;

    #[ORM\Column]
    private int $state;

    #[ORM\Column(type: Types::TEXT)]
    private string $details;

    #[ORM\Column]
    private int $mailcount;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getState():StateType
    {
        return StateType::from($this->state);
    }

    public function setState(StateType $state): static
    {
        $this->state = $state->value;

        return $this;
    }

    public function getDetails(): array
    {
        return unserialize($this->details);
    }

    public function setDetails(array $details): static
    {
        $this->details = serialize($details);

        return $this;
    }

    public function getMailcount(): int
    {
        return $this->mailcount;
    }

    public function setMailcount(int $mailcount): static
    {
        $this->mailcount = $mailcount;

        return $this;
    }
}

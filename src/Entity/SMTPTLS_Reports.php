<?php

namespace App\Entity;

use App\Repository\SMTPTLS_ReportsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SMTPTLS_ReportsRepository::class)]
class SMTPTLS_Reports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domains $domain = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $begin_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column(length: 255)]
    private ?string $organisation = null;

    #[ORM\Column(length: 255)]
    private ?string $contact_info = null;

    #[ORM\Column(length: 255)]
    private ?string $external_id = null;

    #[ORM\OneToMany(mappedBy: 'report', targetEntity: SMTPTLS_Policies::class, orphanRemoval: true)]
    private Collection $SMTPTLS_Policies;

    #[ORM\ManyToMany(targetEntity: Users::class)]
    private Collection $seen;


    public function __construct()
    {
        $this->SMTPTLS_Policies = new ArrayCollection();
        $this->seen = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBeginTime(): ?\DateTimeInterface
    {
        return $this->begin_time;
    }

    public function setBeginTime(\DateTimeInterface $begin_time): static
    {
        $this->begin_time = $begin_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation(string $organisation): static
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getContactInfo(): ?string
    {
        return $this->contact_info;
    }

    public function setContactInfo(string $contact_info): static
    {
        $this->contact_info = $contact_info;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->external_id;
    }

    public function setExternalId(string $external_id): static
    {
        $this->external_id = $external_id;

        return $this;
    }

    /**
     * @return Collection<int, SMTPTLS_Policies>
     */
    public function getSMTPTLS_Policies(): Collection
    {
        return $this->SMTPTLS_Policies;
    }

    public function addSMTPTLSPolicy(SMTPTLS_Policies $SMTPTLS_Policy): static
    {
        if (!$this->SMTPTLS_Policies->contains($SMTPTLS_Policy)) {
            $this->SMTPTLS_Policies->add($SMTPTLS_Policy);
            $SMTPTLS_Policy->setReport($this);
        }

        return $this;
    }

    public function removeSMTPTLSPolicy(SMTPTLS_Policies $SMTPTLS_Policy): static
    {
        if ($this->SMTPTLS_Policies->removeElement($SMTPTLS_Policy)) {
            // set the owning side to null (unless already changed)
            if ($SMTPTLS_Policy->getReport() === $this) {
                $SMTPTLS_Policy->setReport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getSeen(): Collection
    {
        return $this->seen;
    }

    public function addSeen(Users $seen): static
    {
        if (!$this->seen->contains($seen)) {
            $this->seen->add($seen);
        }

        return $this;
    }

    public function removeSeen(Users $seen): static
    {
        $this->seen->removeElement($seen);

        return $this;
    }
}

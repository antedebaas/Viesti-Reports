<?php

namespace App\Entity;

use App\Repository\DMARC_ReportsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DMARC_ReportsRepository::class)]
class DMARC_Reports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $begin_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column(length: 255)]
    private ?string $organisation = null;

    #[ORM\Column(length: 255)]
    private ?string $external_id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $contact_info = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_adkim = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_aspf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_p = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_sp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_pct = null;

    #[ORM\OneToMany(mappedBy: 'Report', targetEntity: DMARC_Records::class, orphanRemoval: true)]
    private Collection $records;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domains $domain = null;

    #[ORM\ManyToMany(targetEntity: Users::class)]
    private Collection $seen;

    public function __construct()
    {
        $this->records = new ArrayCollection();
        $this->seen = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExternalId(): ?string
    {
        return $this->external_id;
    }

    public function setExternalId(string $external_id): static
    {
        $this->external_id = $external_id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getPolicyAdkim(): ?string
    {
        return $this->policy_adkim;
    }

    public function setPolicyAdkim(string $policy_adkim): static
    {
        $this->policy_adkim = $policy_adkim;

        return $this;
    }

    public function getPolicyAspf(): ?string
    {
        return $this->policy_aspf;
    }

    public function setPolicyAspf(string $policy_aspf): static
    {
        $this->policy_aspf = $policy_aspf;

        return $this;
    }

    public function getPolicyP(): ?string
    {
        return $this->policy_p;
    }

    public function setPolicyP(string $policy_p): static
    {
        $this->policy_p = $policy_p;

        return $this;
    }

    public function getPolicySp(): ?string
    {
        return $this->policy_sp;
    }

    public function setPolicySp(string $policy_sp): static
    {
        $this->policy_sp = $policy_sp;

        return $this;
    }

    public function getPolicyPct(): ?string
    {
        return $this->policy_pct;
    }

    public function setPolicyPct(?string $policy_pct): static
    {
        $this->policy_pct = $policy_pct;

        return $this;
    }

    /**
     * @return Collection<int, DMARC_Records>
     */
    public function getDMARC_Records(): Collection
    {
        return $this->records;
    }

    public function addRecord(DMARC_Records $record): static
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
            $record->setReport($this);
        }

        return $this;
    }

    public function removeRecord(DMARC_Records $record): static
    {
        if ($this->records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getReport() === $this) {
                $record->setReport(null);
            }
        }

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

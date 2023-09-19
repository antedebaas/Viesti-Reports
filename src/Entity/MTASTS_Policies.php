<?php

namespace App\Entity;

use App\Repository\MTASTS_PoliciesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MTASTS_PoliciesRepository::class)]
class MTASTS_Policies
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $policy_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_string_version = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $policy_string_mode = null;

    #[ORM\Column(nullable: true)]
    private ?int $policy_string_maxage = null;

    #[ORM\ManyToOne(inversedBy: 'MTASTS_Policies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domains $policy_domain = null;

    #[ORM\Column]
    private ?int $summary_successful_count = null;

    #[ORM\Column]
    private ?int $summary_failed_count = null;

    #[ORM\OneToMany(mappedBy: 'policy', targetEntity: MTASTS_MXRecords::class, orphanRemoval: true)]
    private Collection $MTASTS_MXRecords;

    public function __construct()
    {
        $this->MTASTS_MXRecords = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'MTASTS_Policies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MTASTS_Reports $report = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getReport(): ?MTASTS_Reports
    {
        return $this->report;
    }

    public function setReport(?MTASTS_Reports $report): static
    {
        $this->report = $report;

        return $this;
    }

    public function getPolicyType(): ?string
    {
        return $this->policy_type;
    }

    public function setPolicyType(string $policy_type): static
    {
        $this->policy_type = $policy_type;

        return $this;
    }

    public function getPolicyStringVersion(): ?string
    {
        return $this->policy_string_version;
    }

    public function setPolicyStringVersion(string $policy_string_version): static
    {
        $this->policy_string_version = $policy_string_version;

        return $this;
    }

    public function getPolicyStringMode(): ?string
    {
        return $this->policy_string_mode;
    }

    public function setPolicyStringMode(string $policy_string_mode): static
    {
        $this->policy_string_mode = $policy_string_mode;

        return $this;
    }

    public function getPolicyStringMaxage(): ?int
    {
        return $this->policy_string_maxage;
    }

    public function setPolicyStringMaxage(int $policy_string_maxage): static
    {
        $this->policy_string_maxage = $policy_string_maxage;

        return $this;
    }

    public function getPolicyDomain(): ?Domains
    {
        return $this->policy_domain;
    }

    public function setPolicyDomain(?Domains $policy_domain): static
    {
        $this->policy_domain = $policy_domain;

        return $this;
    }

    public function getSummarySuccessfulCount(): ?int
    {
        return $this->summary_successful_count;
    }

    public function setSummarySuccessfulCount(int $summary_successful_count): static
    {
        $this->summary_successful_count = $summary_successful_count;

        return $this;
    }

    public function getSummaryFailedCount(): ?int
    {
        return $this->summary_failed_count;
    }

    public function setSummaryFailedCount(int $summary_failed_count): static
    {
        $this->summary_failed_count = $summary_failed_count;

        return $this;
    }

    /**
     * @return Collection<int, MTASTS_MXRecords>
     */
    public function getMTASTS_MXRecords(): Collection
    {
        return $this->MTASTS_MXRecords;
    }

    public function addMTASTS_MXRecord(MTASTS_MXRecords $MTASTS_MXRecord): static
    {
        if (!$this->MTASTS_MXRecords->contains($MTASTS_MXRecord)) {
            $this->MTASTS_MXRecords->add($MTASTS_MXRecord);
            $MTASTS_MXRecord->setPolicy($this);
        }

        return $this;
    }

    public function removeMTASTS_MXRecord(MTASTS_MXRecords $MTASTS_MXRecord): static
    {
        if ($this->MTASTS_MXRecords->removeElement($MTASTS_MXRecord)) {
            // set the owning side to null (unless already changed)
            if ($MTASTS_MXRecord->getPolicy() === $this) {
                $MTASTS_MXRecord->setPolicy(null);
            }
        }

        return $this;
    }
}

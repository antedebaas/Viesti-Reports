<?php

namespace App\Entity;

use App\Repository\SMTPTLS_PoliciesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SMTPTLS_PoliciesRepository::class)]
class SMTPTLS_Policies
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

    #[ORM\ManyToOne(inversedBy: 'SMTPTLS_Policies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domains $policy_domain = null;

    #[ORM\Column]
    private ?int $summary_successful_count = null;

    #[ORM\Column]
    private ?int $summary_failed_count = null;

    #[ORM\OneToMany(mappedBy: 'policy', targetEntity: SMTPTLS_MXRecords::class, orphanRemoval: true)]
    private Collection $SMTPTLS_MXRecords;

    public function __construct()
    {
        $this->SMTPTLS_MXRecords = new ArrayCollection();
        $this->SMTPTLS_FailureDetails = new ArrayCollection();
        $this->SMTPTLS_RdataRecords = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'SMTPTLS_Policies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SMTPTLS_Reports $report = null;

    #[ORM\OneToMany(mappedBy: 'policy', targetEntity: SMTPTLS_FailureDetails::class, orphanRemoval: true)]
    private Collection $SMTPTLS_FailureDetails;

    #[ORM\OneToMany(mappedBy: 'policy', targetEntity: SMTPTLS_RdataRecords::class, orphanRemoval: true)]
    private Collection $SMTPTLS_RdataRecords;

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
     * @return Collection<int, SMTPTLS_MXRecords>
     */
    public function getSMTPTLS_MXRecords(): Collection
    {
        return $this->SMTPTLS_MXRecords;
    }

    public function addSMTPTLS_MXRecord(SMTPTLS_MXRecords $SMTPTLS_MXRecord): static
    {
        if (!$this->SMTPTLS_MXRecords->contains($SMTPTLS_MXRecord)) {
            $this->SMTPTLS_MXRecords->add($SMTPTLS_MXRecord);
            $SMTPTLS_MXRecord->setPolicy($this);
        }

        return $this;
    }

    public function removeSMTPTLS_MXRecord(SMTPTLS_MXRecords $SMTPTLS_MXRecord): static
    {
        if ($this->SMTPTLS_MXRecords->removeElement($SMTPTLS_MXRecord)) {
            // set the owning side to null (unless already changed)
            if ($SMTPTLS_MXRecord->getPolicy() === $this) {
                $SMTPTLS_MXRecord->setPolicy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SMTPTLS_FailureDetails>
     */
    public function getSMTPTLS_FailureDetails(): Collection
    {
        return $this->SMTPTLS_FailureDetails;
    }

    public function addSMTPTLSFailureDetail(SMTPTLS_FailureDetails $SMTPTLS_FailureDetail): static
    {
        if (!$this->SMTPTLS_FailureDetails->contains($SMTPTLS_FailureDetail)) {
            $this->SMTPTLS_FailureDetails->add($SMTPTLS_FailureDetail);
            $SMTPTLS_FailureDetail->setPolicy($this);
        }

        return $this;
    }

    public function removeSMTPTLSFailureDetail(SMTPTLS_FailureDetails $SMTPTLS_FailureDetail): static
    {
        if ($this->SMTPTLS_FailureDetails->removeElement($SMTPTLS_FailureDetail)) {
            // set the owning side to null (unless already changed)
            if ($SMTPTLS_FailureDetail->getPolicy() === $this) {
                $SMTPTLS_FailureDetail->setPolicy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SMTPTLS_RdataRecords>
     */
    public function getSMTPTLS_RdataRecords(): Collection
    {
        return $this->SMTPTLS_RdataRecords;
    }

    public function addSMTPTLSRdataRecord(SMTPTLS_RdataRecords $sMTPTLSRdataRecord): static
    {
        if (!$this->SMTPTLS_RdataRecords->contains($sMTPTLSRdataRecord)) {
            $this->SMTPTLS_RdataRecords->add($sMTPTLSRdataRecord);
            $sMTPTLSRdataRecord->setPolicy($this);
        }

        return $this;
    }

    public function removeSMTPTLSRdataRecord(SMTPTLS_RdataRecords $sMTPTLSRdataRecord): static
    {
        if ($this->SMTPTLS_RdataRecords->removeElement($sMTPTLSRdataRecord)) {
            // set the owning side to null (unless already changed)
            if ($sMTPTLSRdataRecord->getPolicy() === $this) {
                $sMTPTLSRdataRecord->setPolicy(null);
            }
        }

        return $this;
    }
}

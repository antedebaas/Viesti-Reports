<?php

namespace App\Entity;

use App\Repository\DomainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainsRepository::class)]
class Domains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fqdn = null;

    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: DMARC_Reports::class, orphanRemoval: true)]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'policy_domain', targetEntity: SMTPTLS_Policies::class, orphanRemoval: true)]
    private Collection $SMTPTLS_Policies;

    #[ORM\OneToMany(mappedBy: 'Domain', targetEntity: MXRecords::class, orphanRemoval: true)]
    private Collection $MXRecords;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
        $this->SMTPTLS_Policies = new ArrayCollection();
        $this->MXRecords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFqdn(): ?string
    {
        return $this->fqdn;
    }

    public function setFqdn(string $fqdn): static
    {
        $this->fqdn = $fqdn;

        return $this;
    }

    /**
     * @return Collection<int, DMARC_Reports>
     */
    public function getDMARC_Reports(): Collection
    {
        return $this->reports;
    }

    public function addReport(DMARC_Reports $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setDomain($this);
        }

        return $this;
    }

    public function removeReport(DMARC_Reports $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getDomain() === $this) {
                $report->setDomain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SMTPTLS_Policies>
     */
    public function getSMTPTLS_Policies(): Collection
    {
        return $this->SMTPTLS_Policies;
    }

    public function addSMTPTLS_Policy(SMTPTLS_Policies $SMTPTLS_Policy): static
    {
        if (!$this->SMTPTLS_Policies->contains($SMTPTLS_Policy)) {
            $this->SMTPTLS_Policies->add($SMTPTLS_Policy);
            $SMTPTLS_Policy->setPolicyDomain($this);
        }

        return $this;
    }

    public function removeSMTPTLS_Policy(SMTPTLS_Policies $SMTPTLS_Policy): static
    {
        if ($this->SMTPTLS_Policies->removeElement($SMTPTLS_Policy)) {
            // set the owning side to null (unless already changed)
            if ($SMTPTLS_Policy->getPolicyDomain() === $this) {
                $SMTPTLS_Policy->setPolicyDomain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MXRecords>
     */
    public function getMXRecords(): Collection
    {
        return $this->MXRecords;
    }

    public function addMXRecord(MXRecords $MXRecord): static
    {
        if (!$this->MXRecords->contains($MXRecord)) {
            $this->MXRecords->add($MXRecord);
            $MXRecord->setDomain($this);
        }

        return $this;
    }

    public function removeMXRecord(MXRecords $MXRecord): static
    {
        if ($this->MXRecords->removeElement($MXRecord)) {
            // set the owning side to null (unless already changed)
            if ($MXRecord->getDomain() === $this) {
                $MXRecord->setDomain(null);
            }
        }

        return $this;
    }
}

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

    #[ORM\OneToMany(mappedBy: 'policy_domain', targetEntity: MTASTS_Policies::class, orphanRemoval: true)]
    private Collection $MTASTS_Policies;

    #[ORM\OneToMany(mappedBy: 'Domain', targetEntity: MXRecords::class, orphanRemoval: true)]
    private Collection $MXRecords;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
        $this->MTASTS_Policies = new ArrayCollection();
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
     * @return Collection<int, MTASTS_Policies>
     */
    public function getMTASTS_Policies(): Collection
    {
        return $this->MTASTS_Policies;
    }

    public function addMTASTS_Policy(MTASTS_Policies $MTASTS_Policy): static
    {
        if (!$this->MTASTS_Policies->contains($MTASTS_Policy)) {
            $this->MTASTS_Policies->add($MTASTS_Policy);
            $MTASTS_Policy->setPolicyDomain($this);
        }

        return $this;
    }

    public function removeMTASTS_Policy(MTASTS_Policies $MTASTS_Policy): static
    {
        if ($this->MTASTS_Policies->removeElement($MTASTS_Policy)) {
            // set the owning side to null (unless already changed)
            if ($MTASTS_Policy->getPolicyDomain() === $this) {
                $MTASTS_Policy->setPolicyDomain(null);
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

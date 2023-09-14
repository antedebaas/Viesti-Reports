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

    public function __construct()
    {
        $this->reports = new ArrayCollection();
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
}

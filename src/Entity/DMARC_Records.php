<?php

namespace App\Entity;

use App\Repository\DMARC_RecordsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DMARC_RecordsRepository::class)]
class DMARC_Records
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DMARC_Reports $Report = null;

    #[ORM\Column(length: 255)]
    private ?string $source_ip = null;

    #[ORM\Column]
    private ?int $count = null;

    #[ORM\Column]
    private ?int $policy_disposition = null;

    #[ORM\Column]
    private ?string $policy_dkim = null;

    #[ORM\Column]
    private ?string $policy_spf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $envelope_to = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $envelope_from = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $header_from = null;







    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $auth_dkim = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $auth_spf = null;

    #[ORM\OneToMany(mappedBy: 'record', targetEntity: DMARC_Results::class, orphanRemoval: true)]
    private Collection $results;

    public function __construct()
    {
        $this->results = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReport(): ?DMARC_Reports
    {
        return $this->Report;
    }

    public function setReport(?DMARC_Reports $Report): static
    {
        $this->Report = $Report;

        return $this;
    }

    public function getSourceIp(): ?string
    {
        return $this->source_ip;
    }

    public function setSourceIp(string $source_ip): static
    {
        $this->source_ip = $source_ip;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getPolicyDisposition(): ?int
    {
        return $this->policy_disposition;
    }

    public function setPolicyDisposition(int $policy_disposition): static
    {
        $this->policy_disposition = $policy_disposition;

        return $this;
    }

    public function getPolicyDkim(): ?string
    {
        return $this->policy_dkim;
    }

    public function setPolicyDkim(?string $policy_dkim): static
    {
        $this->policy_dkim = $policy_dkim;

        return $this;
    }

    public function getPolicySpf(): ?string
    {
        return $this->policy_spf;
    }

    public function setPolicySpf(?string $policy_spf): static
    {
        $this->policy_spf = $policy_spf;

        return $this;
    }

    public function getEnvelopeTo(): ?string
    {
        return $this->envelope_to;
    }

    public function setEnvelopeTo(?string $envelope_to): static
    {
        $this->envelope_to = $envelope_to;

        return $this;
    }

    public function getEnvelopeFrom(): ?string
    {
        return $this->envelope_from;
    }

    public function setEnvelopeFrom(?string $envelope_from): static
    {
        $this->envelope_from = $envelope_from;

        return $this;
    }

    public function getHeaderFrom(): ?string
    {
        return $this->header_from;
    }

    public function setHeaderFrom(?string $header_from): static
    {
        $this->header_from = $header_from;

        return $this;
    }




    public function getAuthDkim(): ?int
    {
        return $this->auth_dkim;
    }

    public function setAuthDkim(int $auth_dkim): static
    {
        $this->auth_dkim = $auth_dkim;

        return $this;
    }

    public function getAuthSpf(): ?int
    {
        return $this->auth_spf;
    }

    public function setAuthSpf(int $auth_spf): static
    {
        $this->auth_spf = $auth_spf;

        return $this;
    }

    /**
     * @return Collection<int, DMARC_Results>
     */
    public function getDMARC_Results(): Collection
    {
        return $this->results;
    }

    public function addResult(DMARC_Results $result): static
    {
        if (!$this->results->contains($result)) {
            $this->results->add($result);
            $result->setRecord($this);
        }

        return $this;
    }

    public function removeResult(DMARC_Results $result): static
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getRecord() === $this) {
                $result->setRecord(null);
            }
        }

        return $this;
    }

}

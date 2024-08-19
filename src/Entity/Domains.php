<?php

namespace App\Entity;

use App\Repository\DomainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainsRepository::class)]
class Domains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $fqdn = null;

    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: DMARC_Reports::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $DMARC_Reports;

    #[ORM\OneToMany(mappedBy: 'policy_domain', targetEntity: SMTPTLS_Policies::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $SMTPTLS_Policies;

    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: SMTPTLS_Reports::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $SMTPTLS_Reports;

    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: MXRecords::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $MXRecords;

    #[ORM\Column(length: 255, options: ['default' => 'STSv1'])]
    private ?string $sts_version = null;

    #[ORM\Column(length: 255, options: ['default' => 'enforce'])]
    private ?string $sts_mode = null;

    #[ORM\Column(options: ['default' => '86400'])]
    private ?int $sts_maxage = null;

    #[ORM\Column(length: 255)]
    private ?string $mailhost = null;

    #[ORM\ManyToMany(targetEntity: Users::class, mappedBy: 'domains')]
    private Collection $users;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bimisvgfile = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bimivmcfile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bimiselector = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dkimselector = null;

    public function __construct()
    {
        $this->DMARC_Reports = new ArrayCollection();
        $this->SMTPTLS_Policies = new ArrayCollection();
        $this->SMTPTLS_Reports = new ArrayCollection();
        $this->MXRecords = new ArrayCollection();
        $this->users = new ArrayCollection();
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
        return $this->DMARC_Reports;
    }

    public function addDMARC_Report(DMARC_Reports $DMARC_Report): static
    {
        if (!$this->DMARC_Reports->contains($DMARC_Report)) {
            $this->DMARC_Reports->add($DMARC_Report);
            $DMARC_Report->setDomain($this);
        }

        return $this;
    }

    public function removeDMARC_Report(DMARC_Reports $DMARC_Report): static
    {
        if ($this->DMARC_Reports->removeElement($DMARC_Report)) {
            // set the owning side to null (unless already changed)
            if ($DMARC_Report->getDomain() === $this) {
                $DMARC_Report->setDomain(null);
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
     * @return Collection<int, SMTPTLS_Reports>
     */
    public function getSMTPTLS_Reports(): Collection
    {
        return $this->SMTPTLS_Reports;
    }

    public function addSMTPTLS_Report(SMTPTLS_Reports $SMTPTLS_Report): static
    {
        if (!$this->SMTPTLS_Reports->contains($SMTPTLS_Report)) {
            $this->SMTPTLS_Reports->add($SMTPTLS_Report);
            $SMTPTLS_Report->setDomain($this);
        }

        return $this;
    }

    public function removeSMTPTLS_Report(SMTPTLS_Reports $SMTPTLS_Report): static
    {
        if ($this->SMTPTLS_Reports->removeElement($SMTPTLS_Report)) {
            // set the owning side to null (unless already changed)
            if ($SMTPTLS_Report->getDomain() === $this) {
                $SMTPTLS_Report->setDomain(null);
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

    public function getStsVersion(): ?string
    {
        return $this->sts_version;
    }

    public function setStsVersion(string $sts_version): static
    {
        $this->sts_version = $sts_version;

        return $this;
    }

    public function getStsMode(): ?string
    {
        return $this->sts_mode;
    }

    public function setStsMode(string $sts_mode): static
    {
        $this->sts_mode = $sts_mode;

        return $this;
    }

    public function getStsMaxage(): ?int
    {
        return $this->sts_maxage;
    }

    public function setStsMaxage(int $sts_maxage): static
    {
        $this->sts_maxage = $sts_maxage;

        return $this;
    }

    public function getMailhost(): ?string
    {
        return $this->mailhost;
    }

    public function setMailhost(string $mailhost): static
    {
        $this->mailhost = $mailhost;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addDomain($this);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeDomain($this);
        }

        return $this;
    }

    public function getBimiSVGFile(): ?string
    {
        return $this->bimisvgfile;
    }

    public function setBimiSVGFile(?string $bimisvgfile): static
    {
        $this->bimisvgfile = $bimisvgfile;

        return $this;
    }

    public function getBimiVMCFile(): ?string
    {
        return $this->bimivmcfile;
    }

    public function setBimiVMCFile(?string $bimivmcfile): static
    {
        $this->bimivmcfile = $bimivmcfile;

        return $this;
    }

    public function getBimiselector(): ?string
    {
        return $this->bimiselector;
    }

    public function setBimiselector(?string $bimiselector): static
    {
        $this->bimiselector = $bimiselector;

        return $this;
    }

    public function getDkimselector(): ?string
    {
        return $this->dkimselector;
    }

    public function setDkimselector(?string $dkimselector): static
    {
        $this->dkimselector = $dkimselector;

        return $this;
    }
}

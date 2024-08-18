<?php

namespace App\Entity;

use App\Repository\SMTPTLS_FailureDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SMTPTLS_FailureDetailsRepository::class)]
class SMTPTLS_FailureDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'SMTPTLS_FailureDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SMTPTLS_Policies $policy = null;

    #[ORM\Column(length: 255)]
    private ?string $result_type = null;

    #[ORM\Column(length: 255)]
    private ?string $sending_mta_ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receiving_ip = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?MXRecords $receiving_mx_hostname = null;

    #[ORM\Column]
    private ?int $failed_session_count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPolicy(): ?SMTPTLS_Policies
    {
        return $this->policy;
    }

    public function setPolicy(?SMTPTLS_Policies $policy): static
    {
        $this->policy = $policy;

        return $this;
    }

    public function getResultType(): ?string
    {
        return $this->result_type;
    }

    public function setResultType(string $result_type): static
    {
        $this->result_type = $result_type;

        return $this;
    }

    public function getSendingMtaIp(): ?string
    {
        return $this->sending_mta_ip;
    }

    public function setSendingMtaIp(string $sending_mta_ip): static
    {
        $this->sending_mta_ip = $sending_mta_ip;

        return $this;
    }

    public function getReceivingIp(): ?string
    {
        return $this->receiving_ip;
    }

    public function setReceivingIp(string $receiving_ip): static
    {
        $this->receiving_ip = $receiving_ip;

        return $this;
    }

    public function getReceivingMxHostname(): ?MXRecords
    {
        return $this->receiving_mx_hostname;
    }

    public function setReceivingMxHostname(?MXRecords $receiving_mx_hostname): static
    {
        $this->receiving_mx_hostname = $receiving_mx_hostname;

        return $this;
    }

    public function getFailedSessionCount(): ?int
    {
        return $this->failed_session_count;
    }

    public function setFailedSessionCount(int $failed_session_count): static
    {
        $this->failed_session_count = $failed_session_count;

        return $this;
    }
}

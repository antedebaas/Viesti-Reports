<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Domains;

class DomainRoleFromIdExtention extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('idtodomain', [$this, 'idtodomain']),
        ];
    }

    public function idtodomain(int $id): string
    {
        $repository = $this->em->getRepository(Domains::class);
        $domain = $repository->findOneBy(array('id' => $id));

        return $domain->getFqdn();
    }
}
<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Domains;

class IdRoleToDomainExtention extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('idroletodomain', [$this, 'idroletodomain']),
        ];
    }

    public function idroletodomain(int $id): string
    {
        $repository = $this->em->getRepository(Domains::class);
        $domain = $repository->findOneBy(array('id' => $id));

        return $domain->getFqdn();
    }
}
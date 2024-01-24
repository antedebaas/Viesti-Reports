<?php

namespace App\Repository;

use App\Entity\DMARC_Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<DMARC_Reports>
 *
 * @method DMARC_Reports|null find($id, $lockMode = null, $lockVersion = null)
 * @method DMARC_Reports|null findOneBy(array $criteria, array $orderBy = null)
 * @method DMARC_Reports[]    findAll()
 * @method DMARC_Reports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DMARC_ReportsRepository extends ServiceEntityRepository
{
    private $security;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, DMARC_Reports::class);
        $this->security = $security;
    }

//    /**
//     * @return DMARC_Reports[] Returns an array of DMARC_Reports objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxDMARC_Results(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DMARC_Reports
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getDomain($report) {
        $domains = array($report->getDomain()->getId());
        return $domains;
    }

    public function getTotalRows(array $domains): int
    {
        $qb = $this->createQueryBuilder('r')
        ->select('count(r.id)');

        if(in_array("ROLE_ADMIN", $this->security->getUser()->getRoles())) {
            $domains=array();
        }

        if(!empty($domains)) {
            $qb->andWhere('r.domain IN (:domains)')
            ->setParameter('domains', $domains);
        }
        return $qb->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }
}

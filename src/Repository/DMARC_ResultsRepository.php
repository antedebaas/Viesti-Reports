<?php

namespace App\Repository;

use App\Entity\DMARC_Results;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DMARC_Results>
 *
 * @method DMARC_Results|null find($id, $lockMode = null, $lockVersion = null)
 * @method DMARC_Results|null findOneBy(array $criteria, array $orderBy = null)
 * @method DMARC_Results[]    findAll()
 * @method DMARC_Results[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DMARC_ResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DMARC_Results::class);
    }

    public function findaligned(string $type, string $result): int
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.result = :result')
            ->setParameter('result', $result)
            ->andWhere('r.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

    //    /**
    //     * @return DMARC_Results[] Returns an array of DMARC_Results objects
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

    //    public function findOneBySomeField($value): ?DMARC_Results
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

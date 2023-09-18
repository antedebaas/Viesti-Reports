<?php

namespace App\Repository;

use App\Entity\MTASTS_Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MTASTS_Reports>
 *
 * @method MTASTS_Reports|null find($id, $lockMode = null, $lockVersion = null)
 * @method MTASTS_Reports|null findOneBy(array $criteria, array $orderBy = null)
 * @method MTASTS_Reports[]    findAll()
 * @method MTASTS_Reports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MTASTS_ReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MTASTS_Reports::class);
    }

//    /**
//     * @return MTASTS_Reports[] Returns an array of MTASTS_Reports objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MTASTS_Reports
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

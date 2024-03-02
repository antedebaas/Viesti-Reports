<?php

namespace App\Repository;

use App\Entity\SMTPTLS_RdataRecords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SMTPTLS_RdataRecords>
 *
 * @method SMTPTLS_RdataRecords|null find($id, $lockMode = null, $lockVersion = null)
 * @method SMTPTLS_RdataRecords|null findOneBy(array $criteria, array $orderBy = null)
 * @method SMTPTLS_RdataRecords[]    findAll()
 * @method SMTPTLS_RdataRecords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SMTPTLS_RdataRecordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SMTPTLS_RdataRecords::class);
    }

//    /**
//     * @return SMTPTLS_RdataRecords[] Returns an array of SMTPTLS_RdataRecords objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SMTPTLS_RdataRecords
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

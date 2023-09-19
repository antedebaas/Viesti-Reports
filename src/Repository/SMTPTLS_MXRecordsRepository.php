<?php

namespace App\Repository;

use App\Entity\SMTPTLS_MXRecords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SMTPTLS_MXRecords>
 *
 * @method SMTPTLS_MXRecords|null find($id, $lockMode = null, $lockVersion = null)
 * @method SMTPTLS_MXRecords|null findOneBy(array $criteria, array $orderBy = null)
 * @method SMTPTLS_MXRecords[]    findAll()
 * @method SMTPTLS_MXRecords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SMTPTLS_MXRecordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SMTPTLS_MXRecords::class);
    }

//    /**
//     * @return SMTPTLS_MXRecords[] Returns an array of SMTPTLS_MXRecords objects
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

//    public function findOneBySomeField($value): ?SMTPTLS_MXRecords
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

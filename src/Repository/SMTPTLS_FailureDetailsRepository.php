<?php

namespace App\Repository;

use App\Entity\SMTPTLS_FailureDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SMTPTLS_FailureDetails>
 *
 * @method SMTPTLS_FailureDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method SMTPTLS_FailureDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method SMTPTLS_FailureDetails[]    findAll()
 * @method SMTPTLS_FailureDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SMTPTLS_FailureDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SMTPTLS_FailureDetails::class);
    }

//    /**
//     * @return SMTPTLS_FailureDetails[] Returns an array of SMTPTLS_FailureDetails objects
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

//    public function findOneBySomeField($value): ?SMTPTLS_FailureDetails
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

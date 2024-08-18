<?php

namespace App\Repository;

use App\Entity\SMTPTLS_Policies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SMTPTLS_Policies>
 *
 * @method SMTPTLS_Policies|null find($id, $lockMode = null, $lockVersion = null)
 * @method SMTPTLS_Policies|null findOneBy(array $criteria, array $orderBy = null)
 * @method SMTPTLS_Policies[]    findAll()
 * @method SMTPTLS_Policies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SMTPTLS_PoliciesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SMTPTLS_Policies::class);
    }

    //    /**
    //     * @return SMTPTLS_Policies[] Returns an array of SMTPTLS_Policies objects
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

    //    public function findOneBySomeField($value): ?SMTPTLS_Policies
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

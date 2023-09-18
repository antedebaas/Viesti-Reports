<?php

namespace App\Repository;

use App\Entity\MTASTS_Policies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MTASTS_Policies>
 *
 * @method MTASTS_Policies|null find($id, $lockMode = null, $lockVersion = null)
 * @method MTASTS_Policies|null findOneBy(array $criteria, array $orderBy = null)
 * @method MTASTS_Policies[]    findAll()
 * @method MTASTS_Policies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MTASTS_PoliciesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MTASTS_Policies::class);
    }

//    /**
//     * @return MTASTS_Policies[] Returns an array of MTASTS_Policies objects
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

//    public function findOneBySomeField($value): ?MTASTS_Policies
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

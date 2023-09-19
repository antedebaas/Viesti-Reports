<?php

namespace App\Repository;

use App\Entity\MXRecords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MXRecords>
 *
 * @method MXRecords|null find($id, $lockMode = null, $lockVersion = null)
 * @method MXRecords|null findOneBy(array $criteria, array $orderBy = null)
 * @method MXRecords[]    findAll()
 * @method MXRecords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MXRecordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MXRecords::class);
    }

//    /**
//     * @return MXRecords[] Returns an array of MXRecords objects
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

//    public function findOneBySomeField($value): ?MXRecords
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

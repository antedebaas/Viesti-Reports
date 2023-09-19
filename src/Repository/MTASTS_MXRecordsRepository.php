<?php

namespace App\Repository;

use App\Entity\MTASTS_MXRecords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MTASTS_MXRecords>
 *
 * @method MTASTS_MXRecords|null find($id, $lockMode = null, $lockVersion = null)
 * @method MTASTS_MXRecords|null findOneBy(array $criteria, array $orderBy = null)
 * @method MTASTS_MXRecords[]    findAll()
 * @method MTASTS_MXRecords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MTASTS_MXRecordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MTASTS_MXRecords::class);
    }

//    /**
//     * @return MTASTS_MXRecords[] Returns an array of MTASTS_MXRecords objects
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

//    public function findOneBySomeField($value): ?MTASTS_MXRecords
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

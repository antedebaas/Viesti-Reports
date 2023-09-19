<?php

namespace App\Repository;

use App\Entity\DMARC_Records;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DMARC_Records>
 *
 * @method DMARC_Records|null find($id, $lockMode = null, $lockVersion = null)
 * @method DMARC_Records|null findOneBy(array $criteria, array $orderBy = null)
 * @method DMARC_Records[]    findAll()
 * @method DMARC_Records[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DMARC_RecordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DMARC_Records::class);
    }

//    /**
//     * @return DMARC_Records[] Returns an array of DMARC_Records objects
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

//    public function findOneBySomeField($value): ?DMARC_Records
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

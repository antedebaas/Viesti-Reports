<?php

namespace App\Repository;

use App\Entity\DoctrineMigrationVersions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DoctrineMigrationVersions>
 *
 * @method DoctrineMigrationVersions|null find($id, $lockMode = null, $lockVersion = null)
 * @method DoctrineMigrationVersions|null findOneBy(array $criteria, array $orderBy = null)
 * @method DoctrineMigrationVersions[]    findAll()
 * @method DoctrineMigrationVersions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineMigrationVersionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctrineMigrationVersions::class);
    }

//    /**
//     * @return DoctrineMigrationVersions[] Returns an array of DoctrineMigrationVersions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxDMARC_Results(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DoctrineMigrationVersions
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\MTASTS_Reports;
use App\Entity\MTASTS_Policies;
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

    // public function findOwnedBy(array $domains, $order ,$pages["perpage"], ($pages["page"]-1)*$pages["perpage"]): array
    // {
    //     //$domains = array("1","2");
    //     $qb = $this->createQueryBuilder('r')
    //     ->select('')
    //     ->addSelect('')->from(MTASTS_Policies::class, 'pol');
    //     if(!empty($domains)) {
    //         $qb->andWhere('r.id = pol.report');
    //         $qb->andWhere('pol.policy_domain IN (:domains)')
    //         ->setParameter('domains', $domains);
    //     }
    //     dump($qb->getQuery()->getSQL());
    //     dd($qb->getQuery());
    //     return $qb->getQuery()
    //         ->getResult()
    //     ;
    // }

    public function findOwnedBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $domains = array("1","2");
        $qb = $this->createQueryBuilder('r')
        ->select('r')
        ->addSelect('')->from(MTASTS_Policies::class, 'p');
        if(!empty($domains)) {
            $qb->andWhere('r.id = p.report');
            $qb->andWhere('p.policy_domain IN (:domains)')
            ->setParameter('domains', $domains);
        }
        foreach($orderBy as $key => $value) {
            $qb->addOrderBy('r.'.$key, $value);
        }
        if(!empty($limit)) {
            $qb->setMaxResults($limit);
        }
        if(!empty($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()->getResult();
    }

    public function getTotalRows(array $domains): int
    {
        $qb = $this->createQueryBuilder('r')
        ->select('count(r.id)')
        ->addSelect('')->from(MTASTS_Policies::class, 'pol');
        if(!empty($domains)) {
            $qb->andWhere('r.id = pol.report');
            $qb->andWhere('pol.policy_domain IN (:domains)')
            ->setParameter('domains', $domains);
        }
        return $qb->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }
}

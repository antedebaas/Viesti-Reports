<?php

namespace App\Repository;

use App\Entity\DMARC_Seen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DMARC_Seen>
 *
 * @method DMARC_Seen|null find($id, $lockMode = null, $lockVersion = null)
 * @method DMARC_Seen|null findOneBy(array $criteria, array $orderBy = null)
 * @method DMARC_Seen[]    findAll()
 * @method DMARC_Seen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DMARC_SeenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DMARC_Seen::class);
    }

//    /**
//     * @return DMARC_Seen[] Returns an array of DMARC_Seen objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxDMARC_Results(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DMARC_Seen
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getSeen(array $reports, int $user)
    {
        $seen = $this
            ->createQueryBuilder('s')
            ->andWhere("s.report IN (:reports)")
            ->andWhere('s.user = :user')
            ->setParameter('reports', array_values($reports))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $seen_report_ids = array();
        foreach($seen as $s)
        {
            $seen_report_ids[] = $s->getReport()->getId();
        }
        return $seen_report_ids;
    }
}

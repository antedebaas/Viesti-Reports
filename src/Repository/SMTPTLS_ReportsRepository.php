<?php

namespace App\Repository;

use App\Entity\SMTPTLS_Reports;
use App\Entity\SMTPTLS_Policies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<SMTPTLS_Reports>
 *
 * @method SMTPTLS_Reports|null find($id, $lockMode = null, $lockVersion = null)
 * @method SMTPTLS_Reports|null findOneBy(array $criteria, array $orderBy = null)
 * @method SMTPTLS_Reports[]    findAll()
 * @method SMTPTLS_Reports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SMTPTLS_ReportsRepository extends ServiceEntityRepository
{
    private $security;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, SMTPTLS_Reports::class);
        $this->security = $security;
    }

//    /**
//     * @return SMTPTLS_Reports[] Returns an array of SMTPTLS_Reports objects
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

//    public function findOneBySomeField($value): ?SMTPTLS_Reports
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getDomain($report) {
        $domains = array();
        $policies = $report->getSMTPTLS_Policies();
        foreach($policies as $policy){
            $domains[] = $policy->getPolicyDomain()->getId();
        }
        return $domains;
    }

    public function findOwnedBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $domains = array();
        foreach ($criteria as $criterion) {
            $domains[] = $criterion->getId();
        }
        $qb = $this->createQueryBuilder('r')
        ->select('r')
        ->addSelect('')->from(SMTPTLS_Policies::class, 'p');
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
           ->select('count(r.id)');
        
        if(in_array("ROLE_ADMIN", $this->security->getUser()->getRoles())) {
            $domains=array();
        }
        
        if(!empty($domains)) {
            $qb->addSelect('')->from(SMTPTLS_Policies::class, 'pol')
               ->andWhere('r.id = pol.report')
               ->andWhere('pol.policy_domain IN (:domains)')
               ->setParameter('domains', $domains);
        }
        return $qb->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }
}

<?php

namespace App\Repository;

use App\Entity\DMARC_Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

use App\Entity\Users;
/**
 * @extends ServiceEntityRepository<DMARC_Reports>
 *
 * @method DMARC_Reports|null find($id, $lockMode = null, $lockVersion = null)
 * @method DMARC_Reports|null findOneBy(array $criteria, array $orderBy = null)
 * @method DMARC_Reports[]    findAll()
 * @method DMARC_Reports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DMARC_ReportsRepository extends ServiceEntityRepository
{
    private $security;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, DMARC_Reports::class);
        $this->security = $security;
    }

    //    /**
    //     * @return DMARC_Reports[] Returns an array of DMARC_Reports objects
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

    //    public function findOneBySomeField($value): ?DMARC_Reports
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getDomain($report)
    {
        $domains = array($report->getDomain()->getId());
        return $domains;
    }

    public function getTotalRows(array $domains): int
    {
        $qb = $this->createQueryBuilder('r')
        ->select('count(r.id)');

        if(in_array("ROLE_ADMIN", $this->security->getUser()->getRoles())) {
            $domains = array();
        }

        if(!empty($domains)) {
            $qb->andWhere('r.domain IN (:domains)')
            ->setParameter('domains', $domains);
        }
        return $qb->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }

    public function getReportsGroupedByMonth(Users $user): array
    {
        $currentDate = new \DateTime();
        $months = array();
        for ($i = 0; $i < 24; $i++) {
            $months[] = $currentDate->format('Y-m');
            $currentDate->modify('-1 month');
        }
        $months = array_reverse($months);

        $result = array();
        foreach($months as $key => $month) {
            $startDate = new \DateTime($month . '-01');
            $endDate = (clone $startDate)->modify('last day of this month');

            if(!in_array("ROLE_ADMIN", $user->getRoles())) {
                $qb_thismonth = $this->createQueryBuilder('r')
                    ->andWhere('r.begin_time >= :start')
                    ->andWhere('r.begin_time <= :end')
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->andWhere('r.domain IN (:domains)')
                    ->setParameter('domains', $user->getDomains())
                    ->getQuery()->getResult()
                ;
            } else {
                $qb_thismonth = $this->createQueryBuilder('r')
                    ->andWhere('r.begin_time >= :start')
                    ->andWhere('r.begin_time <= :end')
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->getQuery()->getResult()
                ;
            }
            
            $result[$month] = $qb_thismonth;

            $totals = array(
                'name' => $month,
                'policy_dkim_fail' => 0,
                'policy_dkim_pass' => 0,
                'policy_spf_fail' => 0,
                'policy_spf_pass' => 0,
            );

            foreach($qb_thismonth as $report) {
                foreach($report->getDMARC_Records() as $record) {
                    switch($record->getPolicyDkim()) {
                        case 'fail':
                            $totals['policy_dkim_fail']++;
                            break;
                        default:
                            $totals['policy_dkim_pass']++;
                            break;
                    }
                    switch($record->getPolicySpf()) {
                        case 'fail':
                            $totals['policy_spf_fail']++;
                            break;
                        default:
                            $totals['policy_spf_pass']++;
                            break;
                    }

                }
            }

            $result[$month] = $totals;
        }
        return $result;
    }
}

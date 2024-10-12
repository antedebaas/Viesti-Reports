<?php

namespace App\Repository;

use App\Entity\SMTPTLS_Reports;
use App\Entity\SMTPTLS_Policies;
use App\Entity\Users;
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

    public function getDomain($report)
    {
        $domains = array();
        $policies = $report->getSMTPTLS_Policies();
        foreach($policies as $policy) {
            $domains[] = $policy->getPolicyDomain()->getId();
        }
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

    public function getReportsGrouped(Users $user, string $timeframe): array
    {
        switch($timeframe) {
            case '3months':
                $daterange = $this->getDateRangesFor('-3 months');
                break;
            case 'lastmonth':
                $daterange = $this->getDateRangesFor('-1 months');
                break;
            case'7days':
                $daterange = $this->getDateRangesFor('-7 days');
                break;
            default:
                $daterange = $this->getDateRangesFor('-3 months');
                break;
        }
        //$daterange = array_reverse($daterange);

        $result = array();
        $global_stats = array(
            'policy_sts' => 0,
            'policy_nopolicy' => 0,
            'stsmode_enforce' => 0,
            'stsmode_testing' => 0,
            'stsmode_none' => 0,
        );
        foreach($daterange as $key => $date) {
            $startDate = new \DateTime($date['start']);
            $endDate = new \DateTime($date['end']);

            if(!in_array("ROLE_ADMIN", $user->getRoles())) {
                $qb = $this->createQueryBuilder('r')
                    ->andWhere('r.begin_time >= :start')
                    ->andWhere('r.begin_time <= :end')
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->andWhere('r.domain IN (:domains)')
                    ->setParameter('domains', $user->getDomains())
                    ->getQuery()->getResult()
                ;
            } else {
                $qb = $this->createQueryBuilder('r')
                    ->andWhere('r.begin_time >= :start')
                    ->andWhere('r.begin_time <= :end')
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->getQuery()->getResult()
                ;
            }
            
            $result['dates'][$date['start']] = $qb;

            $totals = array(
                'name' => $date['start'],
                'policy_sts' => 0,
                'policy_nopolicy' => 0,
                'stsmode_enforce' => 0,
                'stsmode_testing' => 0,
                'stsmode_none' => 0,
            );

            foreach($qb as $report) {
                // foreach($report->getSMTPTLS_Policies() as $record) {
                //     switch($record->getPolicyType()) {
                //         case 'sts':
                //             $totals['policy_sts']++;
                //             $global_stats['policy_sts']++;
                //             break;
                //         default:
                //             $totals['policy_nopolicy']++;
                //             $global_stats['policy_nopolicy']++;
                //             break;
                //     }
                //     switch($record->getPolicyStringMode()) {
                //         case 'enforce':
                //             $totals['stsmode_enforce']++;
                //             $global_stats['stsmode_enforce']++;
                //             break;
                //         case 'testing':
                //             $totals['stsmode_testing']++;
                //             $global_stats['stsmode_testing']++;
                //             break;
                //         default:
                //             $totals['stsmode_none']++;
                //             $global_stats['stsmode_none']++;
                //             break;
                //     }
                // }
            }
            $result['dates'][$date['start']] = $totals;
        }

        if($global_stats['policy_sts'] > $global_stats['policy_nopolicy']) {
            $result['policy_trend'] = true;
        } else {
            $result['policy_trend'] = false;
        }
        $result['policy_total'] = $global_stats['policy_sts'] + $global_stats['policy_nopolicy'];

        if(($global_stats['stsmode_enforce'] > $global_stats['stsmode_testing']) && ($global_stats['stsmode_enforce'] > $global_stats['stsmode_none'])) {
            $result['stsmode_trend'] = true;
        } elseif(($global_stats['stsmode_enforce'] < $global_stats['stsmode_testing']) && ($global_stats['stsmode_enforce'] > $global_stats['stsmode_none'])) {
            $result['stsmode_trend'] = null;
        } else {
            $result['stsmode_trend'] = false;
        }
        $result['stsmode_total'] = $global_stats['stsmode_enforce'] + $global_stats['stsmode_testing'] + $global_stats['stsmode_none'];

        return $result;
    }

    private function getDateRangesFor($range): array
    {
        $currentDate = new \DateTime();
        $startDate = (clone $currentDate)->modify($range);

        $dateRanges = [];

        while ($startDate <= $currentDate) {
            $endDate = clone $startDate;

            $dateRanges[] = [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ];

            $startDate->modify('+1 day');
        }

        return $dateRanges;
    }
}

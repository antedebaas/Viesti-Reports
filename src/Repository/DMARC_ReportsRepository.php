<?php

namespace App\Repository;

use App\Entity\DMARC_Reports;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

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
        $daterange = array_reverse($daterange);

        $result = array();
        $global_stats = array(
            'dkim_pass' => 0,
            'dkim_fail' => 0,
            'spf_pass' => 0,
            'spf_fail' => 0,
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
                'dkim_pass' => 0,
                'dkim_fail' => 0,
                'spf_pass' => 0,
                'spf_fail' => 0,
            );

            foreach($qb as $report) {
                foreach($report->getDMARC_Records() as $record) {
                    switch($record->getPolicyDkim()) {
                        case 'fail':
                            $totals['dkim_fail']++;
                            $global_stats['dkim_fail']++;
                            break;
                        default:
                            $totals['dkim_pass']++;
                            $global_stats['dkim_pass']++;
                            break;
                    }
                    switch($record->getPolicySpf()) {
                        case 'fail':
                            $totals['spf_fail']++;
                            $global_stats['spf_fail']++;
                            break;
                        default:
                            $totals['spf_pass']++;
                            $global_stats['spf_pass']++;
                            break;
                    }
                }
            }
            $result['dates'][$date['start']] = $totals;
        }

        if($global_stats['dkim_pass'] > $global_stats['dkim_fail']) {
            $result['dkim_trend'] = true;
        } else {
            $result['dkim_trend'] = false;
        }
        $result['dkim_total'] = $global_stats['dkim_pass'] + $global_stats['dkim_fail'];

        if($global_stats['spf_pass'] > $global_stats['spf_fail']) {
            $result['spf_trend'] = true;
        } else {
            $result['spf_trend'] = false;
        }
        $result['spf_total'] = $global_stats['spf_pass'] + $global_stats['spf_fail'];

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

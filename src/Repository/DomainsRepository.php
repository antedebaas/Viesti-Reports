<?php

namespace App\Repository;

use App\Entity\Domains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Domains>
 *
 * @method Domains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domains[]    findAll()
 * @method Domains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainsRepository extends ServiceEntityRepository
{
    private UsersRepository $UsersRepository;

    public function __construct(ManagerRegistry $registry, UsersRepository $usersRepository)
    {
        parent::__construct($registry, Domains::class);
        $this->UsersRepository = $usersRepository;
    }

//    /**
//     * @return Domains[] Returns an array of Domains objects
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

//    public function findOneBySomeField($value): ?Domains
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findOwnedBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $domains = array();
        foreach ($criteria as $criterion) {
            if(is_int($criterion)){
                $domains[] = $criterion;
            }
        }
        $qb = $this->createQueryBuilder('d');
        if(!empty($domains)) {
            $qb->andWhere('d.id IN (:domains)')
            ->setParameter('domains', $domains);
        }
        foreach($orderBy as $key => $value) {
            $qb->addOrderBy('d.'.$key, $value);
        }
        if(!empty($limit)) {
            $qb->setMaxResults($limit);
        }
        if(!empty($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()->getResult();
    }

    public function findFormSelectedRoles($options): array
    {
        if(array_key_exists('data', $options) && $options["data"]->getId() != null)
        {
            return $this->findSelectedRoles($options["data"]->getId());
        } else {
            return array();
        }
    }

    public function findSelectedRoles($user_id): array
    {
        $domain_ids=array();
        $user=$this->UsersRepository->find($user_id);
        foreach($user->getRoles() as $domain_id)
        {
            if(is_int($domain_id)){
                $domain_ids[]=$domain_id;
            }
        }

        return $this->createQueryBuilder('d')
            ->andWhere("d.id IN (:domain_ids)")
            ->setParameter('domain_ids', $domain_ids)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTotalRows(): int
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id)')
            ->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }
}

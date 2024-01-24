<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Users>
* @implements PasswordUpgraderInterface<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return Users[] Returns an array of Users objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxDMARC_Results(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Users
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function denyAccessUnlessOwned($domains,$user){
        if(in_array("ROLE_ADMIN",$user->getRoles())){
            return true;
        }
        elseif($this->array_keys_in_array($domains,$this->findDomains($user))){
            return true;
        } else {
            return false;
        }
    }

    private function array_keys_in_array(array $needles, array $haystack) {
        foreach($needles as $needle){
            if(in_array($needle, $haystack)){
                return true;
            } else {
                continue;
            }
        }
        return false;
    }

    public function getTotalRows()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }

    public function findFormIsAdmin($options): bool
    {
        if(array_key_exists('data', $options) && $options["data"]->getId() != null)
        {
            return $this->findIsAdmin($options["data"]->getId());
        } else {
            return false;
        }
    }

    public function findDomains(Users $user){
        $ids = [];
        $domains = $user->getDomains();
        foreach($domains as $domain){
            $ids[] = $domain->getId();
        }
        return $ids;
    }

    public function findIsAdmin($user_id)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.roles')
            ->andWhere("u.id = (:user_id)")
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getOneOrNullResult()['roles']
        ;
        if (in_array("ROLE_ADMIN", $query)) {
            return true;
        } else {
            return false;
        }
    }
}

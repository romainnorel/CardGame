<?php

namespace App\Repository;

use App\Entity\ActiveGames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActiveGames>
 */
class ActiveGamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiveGames::class);
    }

    /**
     * @return ActiveGames[] Returns an array of ActiveGames objects
     */
    public function findByUserId($userId): ?ActiveGames
    {
        return $this->createQueryBuilder('a')
            ->join('a.activeUsers', 'u')
            ->where('u.id =:user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    public function findOneBySomeField($value): ?ActiveGames
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

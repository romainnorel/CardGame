<?php

namespace App\Repository;

use App\Entity\ActiveCards;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActiveCards>
 */
class ActiveCardsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiveCards::class);
    }

    public function findTargetedCard($activeGameId, $position): ?ActiveCards
    {
        return $this->createQueryBuilder('ac')
            ->join('ac.activeGame', 'ag')
            ->where('ag.id =:active_game_id')
            ->andWhere('ac.position =:position')
            ->setParameter('active_game_id', $activeGameId)
            ->setParameter('position', $position)          
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


//    /**
//     * @return ActiveCards[] Returns an array of ActiveCards objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActiveCards
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

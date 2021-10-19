<?php

namespace App\Repository;

use App\Entity\Gammes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gammes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gammes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gammes[]    findAll()
 * @method Gammes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GammesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gammes::class);
    }

    // /**
    //  * @return Gammes[] Returns an array of Gammes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gammes
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\AttributesWithQty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributesWithQty|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributesWithQty|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributesWithQty[]    findAll()
 * @method AttributesWithQty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributesWithQtyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributesWithQty::class);
    }

    // /**
    //  * @return AttributesWithQty[] Returns an array of AttributesWithQty objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttributesWithQty
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

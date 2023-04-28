<?php

namespace App\Repository;

use App\Entity\Abris;
use App\Entity\Discussion;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

   public function search($filter = null) {
        $dql = $this->createQueryBuilder('d')
                ->select('d, a')
                ->leftJoin('d.abris', 'a')

        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['abris']) && '' != $filter['abris']) {
            $slug = $slugify->slugify($filter['abris'], '-');
            $dql->andwhere('a.slug LIKE \'%' . $slug . '%\'');

        }

        if ($filter && isset($filter['userID']) && '' != $filter['userID']) {
            $userID = $filter['userID'];
            $dql->leftJoin('a.createdBy', 'crea');
            $dql->leftJoin('a.proprietaires', 'proprios');
            $dql->leftJoin('a.gestionnaires', 'gests');
            $dql->andWhere('crea.id=\''.$userID.'\' OR proprios.id=\''.$userID.'\' OR gests.id=\''.$userID.'\'' );
        }

        return $dql->getQuery();
    }


    public function listForAbris(Abris $abris): array{
        $dql = $this->createQueryBuilder('d')
                ->where ('d.dysfonctionnement is null')
                ->andWhere('d.abris =:abris')
                ->setParameter('abris', $abris);
        return $dql->getQuery()->getResult();
    }    

    /**
     * massive delete function.
     *
     * @param type $ids
     * 
     */
    public function batchDelete($ids = null)
    {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('c')->delete('App\Entity\Discussion d')->where('d.id IN ('.implode(',', $ids).')');
            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }


    // /**
    //  * @return Discussion[] Returns an array of Discussion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Discussion
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

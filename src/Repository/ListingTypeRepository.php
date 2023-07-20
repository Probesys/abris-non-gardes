<?php

namespace App\Repository;

use App\Entity\ListingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Cocur\Slugify\Slugify;

/**
 * @method ListingType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingType[]    findAll()
 * @method ListingType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListingType::class);
    }

    /**
     * search query.
     *
     * @param type $filter
     *
     * @return type
     */
    public function search($filter = null)
    {
        $dql = $this->createQueryBuilder('c')
          ->select('c')
          // ->leftJoin('c.species', 's')
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['name']) && '' != $filter['name']) {
            $slug = $slugify->slugify($filter['name'], '-');
            $dql->andwhere('c.slug LIKE \'%'.$slug.'%\'');
        }
        if ($filter && isset($filter['type']) && '' != $filter['type']) {
            $dql->andwhere('c.type = '.$filter['type']);
        }

        return $query = $dql->getQuery();
    }

    /**
    * massive delete function.
    *
    * @param type $ids
    */
    public function batchDelete($ids = null)
    {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('ListingType')->delete('App\Entity\ListingType ListingType')->where('ListingType.id IN ('.implode(',', $ids).')');
            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }
}

<?php

namespace App\Repository;

use App\Entity\City;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
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
        ;

        $slugify = new Slugify();
        if ($filter) {
            if (isset($filter['name']) && '' != $filter['name']) {
                $slug = $slugify->slugify($filter['name'], '-');
                $dql->andWhere('c.slug LIKE \'%'.$slug.'%\'');
            }
            if (array_key_exists('zipCode', $filter) && '' != $filter['zipCode']) {
                $dql->andWhere('c.zipCode LIKE \'%'.$filter['zipCode'].'%\'');
            }
            if (array_key_exists('department', $filter) && '' != $filter['department']) {
                $dql->andWhere('c.department LIKE \'%'.$filter['department'].'%\'');
            }
            if (array_key_exists('territory', $filter) && '' != $filter['territory']) {
                $dql->leftJoin('c.territories', 't');
                $dql->andWhere('t.id IN ('.$filter['territory']->getId().')');
            }
        }

        return $dql->getQuery();
    }

    /**
     * massive delete function.
     *
     * @param type $ids
     */
    public function batchDelete($ids = null)
    {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('c')->delete('App\Entity\City c')->where('c.id IN ('.implode(',', $ids).')');
            $query = $queryBuilder->getQuery();
            // queries execution
            $query->execute();
        }
    }

    /**
     *  autocomplete query.
     *
     * @param type $q
     *
     * @return type
     */
    public function autoComplete($q, $page_limit = 30, $page = null)
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($q, '-');
        $dql = $this->createQueryBuilder('c')
            ->select('c.id, c.name, c.zipCode')
            ->orderby('c.slug', 'ASC')
        ;

        if ($q) {
            $dql->Where("c.zipCode LIKE '".$slug."%'");
            $dql->orWhere("c.slug LIKE '".$slug."%'");
        }
        $query = $dql->getQuery();

        $query->setMaxResults($page_limit);

        if ($page) {
            $query->setFirstResult(($page - 1) * $page_limit);
        }

        return $query->getResult();
    }

    /**
     * query to return cities associate to one territory.
     *
     * @param type $territoryId
     *
     * @return type
     */
    public function listTerritoryCities($territoryId, $filterData)
    {
        $dql = $this->createQueryBuilder('c')
            ->distinct()
            ->select('c, t')
            ->leftJoin('c.territories', 't')
            ->where('t.id='.$territoryId)
            ->orderBy('c.name', 'ASC')
        ;
        if (array_key_exists('name', $filterData) && '' != $filterData['name']) {
            $dql->andWHere('c.name LIKE \'%'.$filterData['name'].'%\'');
        }
        if (array_key_exists('zipCode', $filterData) && '' != $filterData['zipCode']) {
            $dql->andWHere('c.zipCode LIKE \'%'.$filterData['zipCode'].'%\'');
        }
        if (array_key_exists('department', $filterData) && '' != $filterData['name']) {
            $dql->andWHere('c.department LIKE \'%'.$filterData['department'].'%\'');
        }

        $query = $dql->getQuery();

        return $query->getResult();
    }

    /**
     * query to return cities associate to one territory and sub territories.
     *
     * @param type $childrens
     *
     * @return type
     */
    public function listSubTerritoryCities($childrens, $filterData)
    {
        $dql = $this->createQueryBuilder('c')
            ->select('c, t')
            ->leftJoin('c.territories', 't')
        ;
        $territoryId = [];
        foreach ($childrens as $teritory) {
            $territoryId[] = $teritory->getId();
        }
        $dql->andWhere('t.id IN ('.implode($territoryId, ',').')');
        if (array_key_exists('name', $filterData) && '' != $filterData['name']) {
            $dql->andWHere('c.name LIKE \'%'.$filterData['name'].'%\'');
        }
        if (array_key_exists('zipCode', $filterData) && '' != $filterData['zipCode']) {
            $dql->andWHere('c.zipCode LIKE \'%'.$filterData['zipCode'].'%\'');
        }
        if (array_key_exists('department', $filterData) && '' != $filterData['name']) {
            $dql->andWHere('c.department LIKE \'%'.$filterData['department'].'%\'');
        }

        $query = $dql->getQuery();

        return $query->getResult();
    }
}

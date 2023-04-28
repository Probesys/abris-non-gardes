<?php

namespace App\Repository;

use App\Entity\Abris;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Cocur\Slugify\Slugify;

/**
 * @method Abris|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abris|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abris[]    findAll()
 * @method Abris[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbrisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abris::class);
    }
    
    /**
     *  autocomplete query.
     *
     * @param type $q
     * @param type $all
     *
     * @return type
     */
    public function autoComplete($q, $page_limit = 30, $page = null)
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($q, '-');
        $dql = $this->createQueryBuilder('a')
            ->select('a.id, a.name')
            ->orderby('a.slug', 'ASC')
            ;

        if ($q) {
            $dql->Where("a.slug LIKE '%".$slug."%'");
        }
        $query = $dql->getQuery();

        $query->setMaxResults($page_limit);

        if ($page) {
            $query->setFirstResult(($page - 1) * $page_limit);
        }

        return $query->getResult();
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
        $dql = $this->createQueryBuilder('a')
          ->select('a')
          
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['name']) && '' != $filter['name']) {
            $slug = $slugify->slugify($filter['name'], '-');
            $dql->andwhere('a.slug LIKE \'%'.$slug.'%\'');
        }
        if ($filter && isset($filter['type']) && '' != $filter['type']) {
            $dql->andwhere('a.type = '.$filter['type']);
        }
        if ($filter && isset($filter['userID']) && '' != $filter['userID']) {
            $userID = $filter['userID'];
            $dql->leftJoin('a.createdBy', 'crea');
            $dql->leftJoin('a.proprietaires', 'proprios');
            $dql->leftJoin('a.gestionnaires', 'gests');
            //$dql->andWhere('crea.id=\''.$userID.'\' OR proprios.id=\''.$userID.'\' OR gests.id=\''.$userID.'\'');
            $dql->andWhere('proprios.id=\''.$userID.'\' OR gests.id=\''.$userID.'\'');
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
            $queryBuilder = $this->createQueryBuilder('Abris')->delete('App\Entity\Abris Abris')->where('Abris.id IN (\''.implode('\',\'', $ids).'\')');

            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }
}

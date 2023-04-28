<?php

namespace App\Repository;

use App\Entity\Territory;

use Cocur\Slugify\Slugify;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class TerritoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Territory::class);
    }
    
    public function search($filter)
    {
        $slugify = new Slugify();
        $dql = $this->createQueryBuilder('t')
            ->select('t.id, t.level, t.name, t.root, t.lft, t.rgt, t.slug')
            ->orderBy('t.root, t.lft', 'ASC');

        if (array_key_exists('name', $filter) && $filter['name']) {
            $slug = $slugify->slugify($filter['name'], '-');
            $dql->andwhere('t.slug LIKE \'%'.$slug.'%\'');
        }

        
        $query = $dql->getQuery();

        return $query;
    }

    public function batchDelete($ids = null)
    {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('t')->delete('App\Entity\Territory t')->where('t.id IN ('.implode(',', $ids).')');          
            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }

      

    public function autoComplete($q, $page_limit = 30, $page = null)
    {
        $dql = $this->createQueryBuilder('t')
            ->select('t.id, t.level, t.name, t.root, t.lft, t.rgt, t.slug')
            ->orderby('t.name', 'ASC')
    ;
        if ($q) {
            $dql->where("t.name LIKE '%".$q."%'");
        }
        
        $query = $dql->getQuery();
        $query->setMaxResults($page_limit);
        if ($page) {
            $query->setFirstResult(($page - 1) * $page_limit);
        }

        return $query->getResult();
    }

    public function getKeyPair()
    {
        $sql = "SELECT t.id, t.name FROM territory t WHERE t.name <> '' ";
       
        $sql .= ' ORDER BY t.name';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}

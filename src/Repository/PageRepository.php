<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Cocur\Slugify\Slugify;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function search($filter = null)
    {
        $dql = $this->createQueryBuilder('p')
              ->select('p')
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['name']) && '' != $filter['name']) {
            $slug = $slugify->slugify($filter['name'], '-');
            $dql->andwhere('p.slug LIKE \'%'.$slug.'%\'');
        }

        return $dql->getQuery();
    }

    public function autoComplete($q, $page_limit = 30, $page = null)
    {
        $filter = [];
        $filter['name'] = $q;
        $query = $this->search($filter);

        $query->setMaxResults($page_limit);

        if ($page) {
            $query->setFirstResult(($page - 1) * $page_limit);
        }

        return $query->getResult();
    }

    /**
     * massive delete function.
     *
     * @param type $ids
     */
    public function batchDelete($ids = null)
    {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('p')->delete('App\Entity\Page p')->where('p.id IN (' . implode(',', $ids) . ')');
            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }
}

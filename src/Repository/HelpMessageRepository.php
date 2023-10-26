<?php

namespace App\Repository;

use App\Entity\HelpMessage;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HelpMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpMessage[]    findAll()
 * @method HelpMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpMessage::class);
    }

    public function search($filter = null)
    {
        $dql = $this->createQueryBuilder('hm')
              ->select('hm')
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['name']) && '' != $filter['name']) {
            $slug = $slugify->slugify($filter['name'], '-');
            $dql->andwhere('hm.slug LIKE \'%'.$slug.'%\'');
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
            $queryBuilder = $this->createQueryBuilder('hm')->delete('App\Entity\HelpMessage hm')->where('hm.id IN ('.implode(',', $ids).')');
            $query = $queryBuilder->getQuery();
            // queries execution
            $query->execute();
        }
    }
}

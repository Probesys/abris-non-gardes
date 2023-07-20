<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Cocur\Slugify\Slugify;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function search($filter = null)
    {
        $dql = $this->createQueryBuilder('u')
              ->select('u')
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['slug']) && '' != $filter['slug']) {
            $slug = $slugify->slugify($filter['slug'], '-');
            $dql->andwhere('u.slug LIKE \'%'.$slug.'%\'');
        }
        if ($filter && isset($filter['role']) && null != $filter['role']) {
            $dql->andwhere('u.roles LIKE \'%'.$filter['role'].'%\'');
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
            $queryBuilder = $this->createQueryBuilder('u')->delete('App\Entity\User u')->where('u.id IN (\'' . implode('\',\'', $ids) . '\')');
            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }
}

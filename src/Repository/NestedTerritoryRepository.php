<?php

namespace App\Repository;

use Cocur\Slugify\Slugify;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class NestedTerritoryRepository extends NestedTreeRepository
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
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
}

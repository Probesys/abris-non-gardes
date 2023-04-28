<?php

namespace App\Repository;

use App\Entity\ListingValue;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;

class ListingValueRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ListingValue::class);
    }

    /**
     * search query.
     *
     * @param type $filter
     *
     * @return type
     */
    public function search($filter = null) {
        $dql = $this->createQueryBuilder('lv')
                ->select('lv, lt,lvp')
                ->leftJoin('lv.listingType', 'lt')
                ->leftJoin('lv.parent', 'lvp')
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['name']) && '' != $filter['name']) {
            $slug = $slugify->slugify($filter['name'], '-');
            $dql->andwhere('lv.slug LIKE \'%' . $slug . '%\'');
        }
        if ($filter && isset($filter['listingType']) && '' != $filter['listingType']) {
            $dql->andwhere('lt.id = ' . $filter['listingType']->getId());
        }
        if ($filter && isset($filter['parent']) && '' != $filter['parent']) {
            $dql->andwhere('lvp.id = ' . $filter['parent']->getId());
        }

        return $dql->getQuery();
    }

    /** Retourne la liste des items fils directs lié à l'item courant
     * @return type
     */
    public function getChildren($parent_id) {
        $dql = $this->createQueryBuilder('lv')
                ->select('lv.id,lv.name,hm.id as id_help_msg')
                ->leftJoin('lv.helpMessage', 'hm')
                ->where('lv.parent=' . $parent_id)
        ;

        return $query = $this->_em->createQuery($dql)->getResult();
    }

    /**
     * Retourne un message d'aide lié s'il existe ou FALSE.
     *
     * @param type $id_liste
     *
     * @return bool
     */
    public function getHelpMessage($id_liste) {
        $dql = $this->createQueryBuilder('lv')
                ->select('hm.id')
                ->join('lv.helpMessage', 'hm')
                ->where('lv.id=' . $id_liste)
        ;

        $query = $dql->getQuery();
        $result = $query->getArrayResult();
        if ((is_countable($result) ? count($result) : 0) > 0) {
            return $query->getArrayResult()[0]['id'];
        } else {
            return false;
        }
    }

    /**
     * massive delete function.
     *
     * @param type $ids
     */
    public function batchDelete($ids = null) {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('ListingValue')
                    ->delete('App\Entity\ListingValue ListingValue')
                    ->where('ListingValue.id IN (' . implode(',', $ids) . ')');
            $query = $queryBuilder->getQuery();

            try {
                $query->execute();
                return true;
            } catch (ForeignKeyConstraintViolationException $exc) {
                return false;
            }
        }
    }

}

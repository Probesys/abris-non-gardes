<?php

namespace App\Repository;

use App\Entity\Message;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function search($filter = null)
    {
        $dql = $this->createQueryBuilder('m')
                ->select('m, d, a')
                ->leftJoin('m.discussion', 'd')
                ->leftJoin('d.abris', 'a')
        ;
        $slugify = new Slugify();
        if ($filter && isset($filter['abris']) && '' != $filter['abris']) {
            $slug = $slugify->slugify($filter['abris'], '-');
            $dql->andwhere('a.slug LIKE \'%'.$slug.'%\'');
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

    public function getLastMessages($user, $limit = null)
    {
        $dql = $this->createQueryBuilder('m')
                ->select('m, d, a')
                ->leftJoin('m.discussion', 'd')
                ->leftJoin('d.abris', 'a')

                ->orderBy('d.created', 'DESC')
        ;
        if ($user->hasRole('ROLE_USER')) {
            $dql->leftJoin('a.createdBy', 'crea')->andWhere('crea.id=\''.$user->getId().'\'');
        }
        if ($user->hasRole('ROLE_OWNER')) {
            $dql->leftJoin('a.proprietaires', 'proprios')
                ->andWhere('proprios.id=\''.$user->getId().'\'');
        }
        if ($user->hasRole('ROLE_MANAGER')) {
            $dql->leftJoin('a.gestionnaires', 'gests')
                ->andWhere('gests.id=\''.$user->getId().'\'');
        }

        $query = $dql->getQuery();
        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }

    public function getUserMessages($userId, $limit = null)
    {
        $dql = $this->createQueryBuilder('m')
                ->select('m')
                ->leftJoin('m.createdBy', 'crea')
                ->where('crea.id=\''.$userId.'\'')
        ;

        $query = $dql->getQuery();
        if ($limit) {
            $query->setMaxResults($limit);
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
            $queryBuilder = $this->createQueryBuilder('m')->delete('App\Entity\Message m')->where('m.id IN (' . implode(',', $ids) . ')');
            $query = $queryBuilder->getQuery();
            //queries execution
            $query->execute();
        }
    }
}

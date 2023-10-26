<?php

namespace App\Repository;

use App\Entity\Dysfonctionnement;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dysfonctionnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dysfonctionnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dysfonctionnement[]    findAll()
 * @method Dysfonctionnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DysfonctionnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dysfonctionnement::class);
    }

    public function search($filter = null)
    {
        $dql = $this->createQueryBuilder('d')
                ->select('d, a, nd, sd, ed')
                ->leftJoin('d.abris', 'a')
                ->leftJoin('d.natureDys', 'nd')
                ->leftJoin('d.statusDys', 'sd')
                ->leftJoin('d.elementDys', 'ed')

        ;
        if ($filter && isset($filter['abris']) && '' != $filter['abris']) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($filter['abris'], '-');
            $dql->andwhere('a.slug LIKE \'%:slug%\'');
            $dql->setParameter('slug', $slug);
        }
        if ($filter && isset($filter['natureDys']) && '' != $filter['natureDys']) {
            $dql->andwhere('d.natureDys = :natureDys');
            $dql->setParameter('natureDys', $filter['natureDys']);
        }
        if ($filter && isset($filter['statusDys']) && '' != $filter['statusDys']) {
            $dql->andwhere('d.statusDys = :statusDys');
            $dql->setParameter('statusDys', $filter['statusDys']);
        }
        if ($filter && isset($filter['userID']) && '' != $filter['userID']) {
            $userID = $filter['userID'];
            $dql->leftJoin('a.createdBy', 'crea');
            $dql->leftJoin('a.proprietaires', 'proprios');
            $dql->leftJoin('a.gestionnaires', 'gests');
            // $dql->andWhere('crea.id=\''.$userID.'\' OR proprios.id=\''.$userID.'\' OR gests.id=\''.$userID.'\'');
            $dql->andWhere('proprios.id=\''.$userID.'\' OR gests.id=\''.$userID.'\'');
        }

        return $dql->getQuery();
    }

    public function getLastDysfonctionnements($user, $limit = null)
    {
        $dql = $this->createQueryBuilder('d')
                ->select('d, a')
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

    /**
     * massive delete function.
     *
     * @param type $ids
     */
    public function batchDelete($ids = null)
    {
        if ($ids) {
            $queryBuilder = $this->createQueryBuilder('c')->delete('App\Entity\Dysfonctionnement d')->where('d.id IN ('.implode(',', $ids).')');
            $query = $queryBuilder->getQuery();
            // queries execution
            $query->execute();
        }
    }
}

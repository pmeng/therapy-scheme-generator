<?php

namespace App\Repository\Therapy;

use App\Entity\Therapy\Label;
use App\Entity\Therapy\Scheme;
use App\Entity\Therapy\Stub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Label $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Label $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findLabelsByRequest(string $query, bool $builder = false): QueryBuilder|array
    {
        $queryBuilder = $this->createQueryBuilder('entity')
            ->leftJoin('entity.stubs', 'stubs')
            ->addSelect('stubs')
            ->orderBy('entity.id', 'ASC')
        ;
        $query = explode(',', $query);

        foreach ($query as $f => $q) {
            $queryBuilder
                ->orWhere("entity.shortName LIKE :filter_$f OR entity.reportName LIKE :filter_$f")
                ->setParameter("filter_$f", $q . '%');
        }

        if ($builder) {
            return $queryBuilder;
        } else {
            return $queryBuilder->getQuery()->getArrayResult();
        }
    }

    public function findLabelsByTemplate(string $query, bool $builder = false): QueryBuilder|array
    {
        $queryBuilder = $this->createQueryBuilder('entity')
            ->leftJoin('entity.stubs', 'stubs')
            ->addSelect('stubs')
            ->andWhere('entity.shortName LIKE :filter OR entity.reportName LIKE :filter')
            ->setParameter('filter', $query . '%')
            ->orderBy('entity.id', 'ASC')
        ;
        if ($builder) {
            return $queryBuilder;
        } else {
            return $queryBuilder->getQuery()->getArrayResult();
        }
    }

    public function loadSavedTemplate(Scheme $scheme): array
    {
        if ($scheme) {
            $labelsList = [];
            $stubsList = [];

            foreach ($scheme->getTargets() as $key => $val) {
                $labelsList[] = $key;
                //$stubsList[] = 
            }

            $queryBuilder = $this->createQueryBuilder('entity')
                ->leftJoin('entity.stubs', 'stubs')
                ->addSelect('stubs')
                ->andWhere('entity.id IN (:labels_list)')
                ->setParameter('labels_list', $labelsList)
                ->orderBy('entity.id', 'ASC')
            ;
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Label[] Returns an array of Label objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Label
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

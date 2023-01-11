<?php

namespace App\Repository\Therapy;

use App\Entity\Therapy\Scheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scheme>
 *
 * @method Scheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scheme[]    findAll()
 * @method Scheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scheme::class);
    }

    public function save(Scheme $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Scheme $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTemplatesListQuery(): Query
    {
        $queryBuilder = $this->createQueryBuilder('tpl');
        
        return $queryBuilder->getQuery();
    }

//    /**
//     * @return Scheme[] Returns an array of Scheme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Scheme
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

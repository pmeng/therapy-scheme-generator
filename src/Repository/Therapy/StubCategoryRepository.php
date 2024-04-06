<?php

namespace App\Repository\Therapy;

use App\Entity\Therapy\Scheme;
use App\Entity\Therapy\StubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StubCategory>
 *
 * @method ٍStubCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ٍStubCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ٍStubCategory[]    findAll()
 * @method ٍStubCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StubCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StubCategory::class);
    }

    public function save(StubCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StubCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}

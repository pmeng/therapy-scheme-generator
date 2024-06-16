<?php

namespace App\Repository\Therapy;

use App\Entity\Therapy\SchemeSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchemeSetting>
 *
 * @method SchemeSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchemeSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchemeSetting[]    findAll()
 * @method SchemeSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchemeSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchemeSetting::class);
    }

    public function save(SchemeSetting $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(SchemeSetting $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}

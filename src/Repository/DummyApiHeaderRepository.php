<?php

namespace App\Repository;

use App\Entity\DummyApiHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DummyApiHeader>
 *
 * @method DummyApiHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method DummyApiHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method DummyApiHeader[]    findAll()
 * @method DummyApiHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DummyApiHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DummyApiHeader::class);
    }

    public function add(DummyApiHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DummyApiHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DummyApiHeader[] Returns an array of DummyApiHeader objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DummyApiHeader
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\DummyApiEndpoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DummyApiEndpoint>
 *
 * @method DummyApiEndpoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method DummyApiEndpoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method DummyApiEndpoint[]    findAll()
 * @method DummyApiEndpoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DummyApiEndpointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DummyApiEndpoint::class);
    }

    public function add(DummyApiEndpoint $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DummyApiEndpoint $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DummyApiEndpoint[] Returns an array of DummyApiEndpoint objects
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

//    public function findOneBySomeField($value): ?DummyApiEndpoint
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

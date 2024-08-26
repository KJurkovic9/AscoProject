<?php

namespace App\Repository;

use App\Entity\PreCalculation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PreCalculation>
 *
 * @method PreCalculation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreCalculation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreCalculation[]    findAll()
 * @method PreCalculation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreCalculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreCalculation::class);
    }

    //    /**
    //     * @return PreCalculation[] Returns an array of PreCalculation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PreCalculation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

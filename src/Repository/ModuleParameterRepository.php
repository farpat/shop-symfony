<?php

namespace App\Repository;

use App\Entity\ModuleParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ModuleParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleParameter[]    findAll()
 * @method ModuleParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleParameter::class);
    }

    // /**
    //  * @return ModuleParameter[] Returns an array of ModuleParameter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModuleParameter
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

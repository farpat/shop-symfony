<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Visit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visit[]    findAll()
 * @method Visit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    // /**
    //  * @return Visit[] Returns an array of Visit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Visit
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function getLastVisit(?User $user, string $ipAddress): ?Visit
    {
        return $this->findOneBy(
            ['user' => $user, 'ipAddress' => $ipAddress],
            ['createdAt' => 'DESC']
        );
    }

    public function getVisits(User $user, \DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->andWhere('v.createdAt BETWEEN :start and :end')
            ->setParameters([
                'user'  => $user,
                'start' => $start,
                'end'   => $end
            ])
            ->getQuery()
            ->getArrayResult();
    }
}

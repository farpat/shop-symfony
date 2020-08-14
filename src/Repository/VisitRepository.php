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

    public function getLastVisit(?User $user, string $ipAddress): ?Visit
    {
        return $this->findOneBy(
            ['user' => $user, 'ipAddress' => $ipAddress],
            ['createdAt' => 'DESC']
        );
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getVisitsCount(User $user, \DateTime $start, \DateTime $end): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.user = :user')
            ->andWhere('v.createdAt BETWEEN :start and :end')
            ->setParameters([
                'user'  => $user,
                'start' => $start,
                'end'   => $end
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getVisits(User $user, \DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id) as count, v.url')
            ->andWhere('v.user = :user')
            ->andWhere('v.createdAt BETWEEN :start and :end')
            ->setParameters([
                'user'  => $user,
                'start' => $start,
                'end'   => $end
            ])
            ->groupBy('v.url')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

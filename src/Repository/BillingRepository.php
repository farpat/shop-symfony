<?php

namespace App\Repository;

use App\Entity\Billing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Billing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Billing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Billing[]    findAll()
 * @method Billing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillingRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Billing::class);
    }

    public function getWithAllRelations (string $billingNumber): ?Billing
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'u', 'a', 'i', 'pr')
            ->leftJoin('b.user', 'u')
            ->leftJoin('b.delivered_address', 'a')
            ->leftJoin('b.items', 'i')
            ->leftJoin('i.product_reference', 'pr')
            ->where('b.number = :number')
            ->setParameter('number', $billingNumber)
            ->getQuery()->getOneOrNullResult();
    }
}

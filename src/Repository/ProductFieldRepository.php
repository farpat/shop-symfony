<?php

namespace App\Repository;

use App\Entity\ProductField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductField[]    findAll()
 * @method ProductField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductField::class);
    }
}

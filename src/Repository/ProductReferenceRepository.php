<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductReference[]    findAll()
 * @method ProductReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductReference::class);
    }
}

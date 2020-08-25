<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[]
     * @throws Exception
     */
    public function getProductsInHome($ids): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'mainImage', 'category', 'productReferences')
            ->leftJoin('p.category', 'category')
            ->leftJoin('p.mainImage', 'mainImage')
            ->leftJoin('p.productReferences', 'productReferences')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $term
     *
     * @return Product[]
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'mainImage', 'category', 'productReferences')
            ->leftJoin('p.mainImage', 'mainImage')
            ->leftJoin('p.category', 'category')
            ->leftJoin('p.productReferences', 'productReferences')
            ->where('p.label LIKE :label')
            ->setMaxResults(5)
            ->groupBy('p.id')
            ->setParameter('label', "%$term%")
            ->getQuery()
            ->getResult();
    }

    public function getWithAllRelations(int $productId): ?Product
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'category', 'productReferences', 'images', 'mainImage')
            ->leftJoin('p.category', 'category')
            ->leftJoin('p.productReferences', 'productReferences')
            ->leftJoin('productReferences.images', 'images')
            ->leftJoin('productReferences.mainImage', 'mainImage')
            ->where('p.id = :id')
            ->setParameter('id', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getProductsForMenu(array $ids): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'category')
            ->leftJoin('p.category', 'category')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}

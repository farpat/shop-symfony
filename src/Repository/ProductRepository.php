<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\Product;
use App\Entity\ProductField;
use App\Entity\ProductReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[]
     * @throws \Exception
     */
    public function getProductsInHome (): array
    {
        $productIdsInHomepageParameter = $this->_em->getRepository(Module::class)->getParameter('home', 'products');
        if ($productIdsInHomepageParameter === null) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->leftJoin('p.mainImage', 'i')
            ->where('p.id IN (:ids)')
            ->setParameters([
                'ids' => $productIdsInHomepageParameter->getValue()
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $term
     *
     * @return Product[]
     */
    public function search (string $term): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'i', 'c', 'pr')
            ->leftJoin('p.mainImage', 'i')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.productReferences', 'pr')
            ->where('p.label LIKE :label')
            ->setMaxResults(5)
            ->groupBy('p.id')
            ->setParameter('label', "%$term%")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Product $product
     *
     * @return ProductField[]
     */
    public function getProductFields (Product $product): array
    {
        if ($product->getProductReferences()->isEmpty()) {
            return [];
        }

        /** @var ProductReference $firstProductReference */
        $firstProductReference = $product->getProductReferences()->first();
        $productFieldsIds = array_keys($firstProductReference->getFilledProductFields());

        return $this->getEntityManager()->createQueryBuilder()
            ->select('pr')
            ->from(ProductReference::class, 'pr')
            ->where('pr.id IN (:ids)')
            ->setParameter('ids', $productFieldsIds)
            ->getQuery()
            ->getResult();

    }

    public function getWithAllRelations (int $productId): ?Product
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.productReferences', 'pr')
            ->leftJoin('pr.images', 'i')
            ->where('p.id = :id')
            ->setParameters([
                'id' => $productId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}

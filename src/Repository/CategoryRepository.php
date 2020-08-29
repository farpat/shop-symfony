<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     * @throws Exception
     */
    public function getCategoriesInHome(array $ids): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'image')
            ->leftJoin('c.image', 'image')
            ->where('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function search(string $term): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'image')
            ->leftJoin('c.image', 'image')
            ->where('c.label LIKE :label')
            ->setMaxResults(2)
            ->setParameter('label', "%$term%")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $categoryId
     *
     * @return Category|null
     * @throws NonUniqueResultException
     */
    public function getWithAllRelations(int $categoryId): ?Category
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'image', 'productFields')
            ->leftJoin('c.image', 'image')
            ->leftJoin('c.productFields', 'productFields')
            ->where('c.id = :id')
            ->setParameter('id', $categoryId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    public function getProducts(Category $category): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('product', 'category', 'mainImage', 'productReferences')
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
            ->leftJoin('product.mainImage', 'mainImage')
            ->leftJoin('product.productReferences', 'productReferences')
            ->where(
                $this->getEntityManager()->getExpressionBuilder()->in('product.category',
                    $this->getEntityManager()->createQueryBuilder()
                        ->select('category2.id')
                        ->from(Category::class, 'category2')
                        ->where('category2.nomenclature = :nomenclature OR category2.nomenclature LIKE :nomenclatureExpression')
                        ->getDQL()
                )
            )
            ->setParameters([
                'nomenclature'           => $category->getNomenclature(),
                'nomenclatureExpression' => "{$category->getNomenclature()}.%"
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Category[]
     */
    public function getRootCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'image')
            ->leftJoin('c.image', 'image')
            ->where('(LENGTH(c.nomenclature) - LENGTH(REPLACE(c.nomenclature, \'.\', \'\'))) + 1 = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Category $parentCategory
     *
     * @return Category[]
     */
    public function getChildren(Category $parentCategory): array
    {
        if ($parentCategory->getIsLast()) {
            return [];
        }

        return $this->createQueryBuilder('c')
            ->where('c.nomenclature LIKE :nomenclatureExpression')
            ->andWhere('LENGTH(c.nomenclature) - LENGTH(REPLACE(c.nomenclature,\'.\',\'\')) + 1 = :level')
            ->setParameters([
                'nomenclatureExpression' => "{$parentCategory->getNomenclature()}.%",
                'level'                  => $parentCategory->getLevel() + 1
            ])
            ->getQuery()
            ->getResult();
    }

    public function getCategoriesForMenu(array $ids): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}

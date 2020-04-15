<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Module;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     * @throws \Exception
     */
    public function getCategoriesInHome (): array
    {
        $categoryIdsInHomeParameter = $this->_em->getRepository(Module::class)->getParameter('home', 'categories');
        if ($categoryIdsInHomeParameter === null) {
            return [];
        }

        return $this->createQueryBuilder('c')
            ->select('c', 'i')
            ->leftJoin('c.image', 'i')
            ->where('c.id IN (:ids)')
            ->setParameters([
                'ids' => $categoryIdsInHomeParameter->getValue()
            ])
            ->getQuery()
            ->getResult();
    }

    public function search (string $term): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'i')
            ->leftJoin('c.image', 'i')
            ->where('c.label LIKE :label')
            ->setMaxResults(2)
            ->setParameters([
                'label' => "%$term%"
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $categoryId
     *
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getWithAllRelations (int $categoryId): ?Category
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'i', 'pf')
            ->leftJoin('c.image', 'i')
            ->leftJoin('c.productFields', 'pf')
            ->where('c.id = :id')
            ->setParameters([
                'id' => $categoryId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    public function getProducts (Category $category): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p', 'c', 'i', 'pr')
            ->from(Product::class, 'p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.mainImage', 'i')
            ->leftJoin('p.productReferences', 'pr')
            ->where(
                $this->getEntityManager()->getExpressionBuilder()->in('p.category',
                    $this->getEntityManager()->createQueryBuilder()
                        ->select('c2.id')
                        ->from(Category::class, 'c2')
                        ->where('c2.nomenclature = :nomenclature OR c2.nomenclature LIKE :nomenclatureExpression')
                        ->getDQL()
                )
            )
            ->setParameters([
                'nomenclature'           => $category->getNomenclature(),
                'nomenclatureExpression' => "{$category->getNomenclature()}.%"
            ])
            ->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getRootCategories (): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'i')
            ->leftJoin('c.image', 'i')
            ->where('(LENGTH(c.nomenclature) - LENGTH(REPLACE(c.nomenclature, \'.\', \'\'))) + 1 = 1')
            ->getQuery()->getResult();
    }

    /**
     * @param Category $parentCategory
     *
     * @return Category[]
     */
    public function getChildren (Category $parentCategory): array
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
            ->getQuery()->getResult();
    }
}

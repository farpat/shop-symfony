<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Module;
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

    public function getCategoriesInHome ()
    {
        $categoryIdsInHomeParameter = $this->_em->getRepository(Module::class)->getParameter('home', 'categories');
        if ($categoryIdsInHomeParameter === null) {
            return [];
        }

        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.image', 'i')
            ->where('c.id IN (:ids)')
            ->setParameters([
                'ids' => $categoryIdsInHomeParameter->getValue()
            ])
            ->getQuery()
            ->getResult();
    }
}

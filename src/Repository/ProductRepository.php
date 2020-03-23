<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\Product;
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

    public function getProductsInHome ()
    {
        $productIdsInHomepageParameter = $this->_em->getRepository(Module::class)->getParameter('home', 'products');
        if ($productIdsInHomepageParameter === null) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->leftJoin('p.main_image', 'i')
            ->where('p.id IN (:ids)')
            ->setParameters([
                'ids' => $productIdsInHomepageParameter->getValue()
            ])
            ->getQuery()
            ->getResult();
    }
}

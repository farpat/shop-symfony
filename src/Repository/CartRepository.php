<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function getCart(User $user): ?Cart
    {
        return $this->createQueryBuilder('cart')
            ->select('cart', 'items', 'productReference', 'mainImage', 'images', 'user', 'product', 'category')
            ->leftJoin('cart.user', 'user')
            ->leftJoin('cart.items', 'items')
            ->leftJoin('items.productReference', 'productReference')
            ->leftJoin('productReference.images', 'images')
            ->leftJoin('productReference.mainImage', 'mainImage')
            ->leftJoin('productReference.product', 'product')
            ->leftJoin('product.category', 'category')
            ->where('user = :user')
            ->andWhere('cart.webhookPaymentId is NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

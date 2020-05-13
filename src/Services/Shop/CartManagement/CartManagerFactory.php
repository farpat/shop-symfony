<?php

namespace App\Services\Shop\CartManagement;


use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CartManagerFactory
{
    private ?UserInterface $user;
    private EntityManagerInterface $entityManager;
    private ProductReferenceRepository $productReferenceRepository;
    private RequestStack $requestStack;
    private NormalizerInterface $normalizer;
    private CartRepository $cartRepository;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        ProductReferenceRepository $productReferenceRepository,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        CartRepository $cartRepository
    ) {
        $this->user = $security->getUser();
        $this->entityManager = $entityManager;
        $this->productReferenceRepository = $productReferenceRepository;
        $this->requestStack = $requestStack;
        $this->normalizer = $normalizer;
        $this->cartRepository = $cartRepository;
    }

    public function createCartManagerInterface(): CartManagerInterface
    {
        if ($this->user instanceof User) {
            return new CartManagerInDatabase(
                $this->entityManager,
                $this->productReferenceRepository,
                $this->cartRepository,
                $this->user,
                $this->normalizer
            );
        }

        return new CartManagerInCookie(
            $this->entityManager,
            $this->productReferenceRepository,
            $this->requestStack->getCurrentRequest(),
            $this->normalizer
        );
    }
}
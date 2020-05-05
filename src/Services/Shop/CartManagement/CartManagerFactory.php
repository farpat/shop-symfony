<?php

namespace App\Services\Shop\CartManagement;


use App\Entity\User;
use App\Repository\ProductReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CartManagerFactory
{
    private ?UserInterface $user;
    private EntityManagerInterface $entityManager;
    private ProductReferenceRepository $productReferenceRepository;
    private RequestStack $requestStack;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct (Security $security, EntityManagerInterface $entityManager, ProductReferenceRepository $productReferenceRepository, RequestStack $requestStack, SerializerInterface $serializer)
    {
        $this->user = $security->getUser();
        $this->entityManager = $entityManager;
        $this->productReferenceRepository = $productReferenceRepository;
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;
    }

    public function createCartManagerInterface (): CartManagerInterface
    {
        if ($this->user instanceof User) {
            return new CartManagerInDatabase(
                $this->entityManager,
                $this->productReferenceRepository,
                $this->user,
                $this->serializer
            );
        }

        return new CartManagerInCookie(
            $this->entityManager,
            $this->productReferenceRepository,
            $this->requestStack->getCurrentRequest(),
            $this->serializer
        );
    }
}
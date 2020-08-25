<?php

namespace App\Controller\Api\Profile;

use App\Repository\CategoryRepository;
use App\Repository\ProductFieldRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/profile/admin", name="app_api_profile_admin_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/products", name="products", methods={"GET"})
     */
    public function products(CategoryRepository $categoryRepository, ProductFieldRepository $productFieldRepository)
    {
        return $this->json([
            'productFields' => $productFieldRepository->getAllForApi(),
            'categories'    => $categoryRepository->getAllForApi()
        ], 200);
    }
}

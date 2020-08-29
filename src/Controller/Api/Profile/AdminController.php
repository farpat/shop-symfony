<?php

namespace App\Controller\Api\Profile;

use App\Repository\ProductFieldRepository;
use App\Services\Shop\CategoryService;
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
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function categories(CategoryService $categoryService, ProductFieldRepository $productFieldRepository)
    {
        return $this->json([
            'productFields' => $productFieldRepository->getAllForApi(),
            'categories'    => $categoryService->generateListForCategoryIndexAdmin($categoryService->getRootCategories())
        ], 200);
    }
}

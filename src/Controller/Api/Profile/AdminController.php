<?php

namespace App\Controller\Api\Profile;

use App\Entity\Category;
use App\Repository\ProductFieldRepository;
use App\Services\Shop\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/profile/admin", name="app_api_profile_admin_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CategoryService        $categoryService;

    public function __construct(EntityManagerInterface $entityManager, CategoryService $categoryService)
    {
        $this->entityManager = $entityManager;
        $this->categoryService = $categoryService;
    }


    /**
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function categories(ProductFieldRepository $productFieldRepository)
    {
        return $this->json([
            'categories'    => $this->categoryService->generateListForCategoryIndexAdmin(
                $this->categoryService->getRootCategories()
            )
        ]);
    }

    /**
     * @Route("/categories/{category}/edit", name="editCategory", methods={"PUT"})
     */
    public function editCategory(Request $request, Category $category)
    {
        $nomenclature = trim($request->request->get('nomenclature'));

        if ($category->getNomenclature() !== $nomenclature) {
            /** @var Category[] $childCategories */
            $childCategories = $this->entityManager->getRepository(Category::class)->getChildren($category);
            $from = sprintf("/%s/", preg_quote($category->getNomenclature() . '.'));
            $to = $nomenclature . '.';

            foreach ($childCategories as $childCategory) {
                $childCategory->setNomenclature(preg_replace($from, $to, $childCategory->getNomenclature(), 1));
            }
        }


        $category
            ->setDescription($request->request->get('description'))
            ->setNomenclature($nomenclature)
            ->setLabel($request->request->get('label'));

        $this->entityManager->flush();

        return $this->json(
            $this->categoryService->generateListForCategoryIndexAdmin(
                $this->categoryService->getRootCategories(),
                true,
                true)
        );
    }

    /**
     * @Route("/categories/new", name="addCategory", methods={"POST"})
     */
    public function addCategory(Request $request, Category $childCategory)
    {
        $category = new Category;
        $category
            ->setDescription($request->request->get('description'))
            ->setNomenclature($request->request->get('nomenclature'))
            ->setLabel($request->request->get('label'));

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->json(
            $this->categoryService->generateListForCategoryIndexAdmin(
                $this->categoryService->getRootCategories(),
                true,
                true)
        );
    }
}

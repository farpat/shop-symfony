<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Services\Shop\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(name="app_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct (CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/categories", name="index", methods={"GET"})
     */
    public function index (CategoryService $categoryService)
    {
        $html = $categoryService->generateHtml($this->categoryRepository->getRootCategories());

        $breadcrumb = [
            ['label' => 'Category']
        ];

        return $this->render('category/index.html.twig', compact('html', 'breadcrumb'));
    }

    /**
     * @Route("/categories/{categorySlug}-{categoryId}", name="show", methods={"GET"}, requirements={"categorySlug":"[a-z\d\-]+", "categoryId":"\d+"})g
     * @Entity("category", expr="repository.getWithAllRelations(categoryId)")
     */
    public function show (Category $category, string $categorySlug, Request $request, CategoryRepository $categoryRepository, SerializerInterface $serializer)
    {
        $categoryShowUrl = $this->generateUrl('app_category_show', [
            'categoryId'   => $category->getId(),
            'categorySlug' => $category->getSlug(),
        ]);

        $currentPage = $request->query->getInt('page');
        if ($currentPage === 1 || $categorySlug !== $category->getSlug()) {
            return $this->redirect($categoryShowUrl);
        }

        $productFields = $category->getProductFields();
        $products = $categoryRepository->getProducts($category);

        $productFieldsInJson = $serializer->serialize($productFields, 'json');
        $productsInJson = $serializer->serialize($products, 'json');

        $breadcrumb = [
            ['label' => 'Category', 'url' => $categoryShowUrl],
            ['label' => $category->getLabel()]
        ];

        return $this->render('category/show.html.twig', compact('category', 'currentPage', 'productFieldsInJson', 'productsInJson', 'breadcrumb'));
    }
}

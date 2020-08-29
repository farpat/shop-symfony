<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Entity\Product;
use App\Services\Shop\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(name="app_front_category_")
 */
class CategoryController extends AbstractController
{

    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/categories", name="index", methods={"GET"})
     */
    public function index()
    {
        $breadcrumb = [
            ['label' => 'Category']
        ];

        return $this->render('category/index.html.twig', [
            'breadcrumb' => $breadcrumb,
            'html'       => $this->categoryService->generateHtmlForCategoryIndex($this->categoryService->getRootCategories())
        ]);
    }

    /**
     * @Route("/categories/{categorySlug}-{categoryId}", name="show", methods={"GET"}, requirements={"categorySlug":"[a-z\d\-]+", "categoryId":"\d+"})g
     * @Entity("category", expr="repository.getWithAllRelations(categoryId)")
     */
    public function show(Category $category, string $categorySlug, Request $request, SerializerInterface $serializer)
    {
        $currentPage = $request->query->get('page');

        if (
            ($currentPage !== null && filter_var($currentPage, FILTER_VALIDATE_INT) === false) ||
            $currentPage === '1' ||
            $categorySlug !== $category->getSlug()
        ) {
            return $this->redirect($this->generateUrl('app_front_category_show', [
                'categoryId'   => $category->getId(),
                'categorySlug' => $category->getSlug(),
            ]));
        }

        $breadcrumb = [
            ['label' => 'Category', 'url' => $this->generateUrl('app_front_category_index')],
            ['label' => $category->getLabel()]
        ];

        return $this->render('category/show.html.twig', [
            'category'            => $category,
            'currentPage'         => $currentPage > 0 ? $currentPage : 1,
            'perPage'             => Product::PER_PAGE,
            'productFieldsInJson' => $serializer->serialize($category->getProductFields(), 'json'),
            'productsInJson'      => $serializer->serialize($this->categoryService->getProducts($category), 'json'),
            'breadcrumb'          => $breadcrumb
        ]);
    }
}

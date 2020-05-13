<?php

namespace App\Controller\Front;

use App\Repository\{CategoryRepository, ModuleRepository, ProductRepository};
use App\Services\ModuleService;
use App\Services\Shop\CartManagement\CartManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route(name="app_home_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param ModuleRepository $moduleRepository
     *
     * @return Response
     * @throws Exception
     */
    public function index(ModuleService $moduleService, CartManagerInterface $cartManager)
    {
        $elementsToDisplayInHomepageParameter = $moduleService->getParameter('home', 'display');

        $elementsToDisplayInHomepage = $elementsToDisplayInHomepageParameter !== null ?
            $elementsToDisplayInHomepageParameter->getValue() :
            [];

        return $this->render('home/index.html.twig', ['elementsToDisplayInHomepage' => $elementsToDisplayInHomepage]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function search(
        Request $request,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        NormalizerInterface $normalizer
    ) {
        $term = $request->query->get('q');
        if ($term === null || strlen((string)$term) === 1) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        $categories = $normalizer->normalize($categoryRepository->search($term), 'search');
        $products = $normalizer->normalize($productRepository->search($term), 'search');

        return $this->json(array_merge($categories, $products));
    }
}

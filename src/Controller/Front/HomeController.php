<?php

namespace App\Controller\Front;

use App\Repository\{CategoryRepository, ProductRepository};
use App\Services\ModuleService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route(name="app_front_home_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     * @throws Exception
     */
    public function index(ModuleService $moduleService)
    {
        $elementsToDisplayInHomepageParameter = $moduleService->getParameter('home', 'display');

        return $this->render('home/index.html.twig', [
            'elementsToDisplayInHomepage' => $elementsToDisplayInHomepageParameter->getValue()
        ]);
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
        if ($term === null || strlen((string)$term) <= 2) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        $categories = $normalizer->normalize($categoryRepository->search($term), 'search');
        $products = $normalizer->normalize($productRepository->search($term), 'search');

        return $this->json(array_merge($categories, $products));
    }
}

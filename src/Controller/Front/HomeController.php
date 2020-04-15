<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index (ModuleRepository $moduleRepository, ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $elementsToDisplayInHomepageParameter = $moduleRepository->getParameter('home', 'display');

        $elementsToDisplayInHomepage = $elementsToDisplayInHomepageParameter !== null ?
            $elementsToDisplayInHomepageParameter->getValue() :
            [];

        return $this->render('home/index.html.twig', compact('elementsToDisplayInHomepage'));
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search (Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository, NormalizerInterface $normalizer)
    {
        $term = $request->query->get('q');
        if ($term === null) {
            return $this->json([], 400);
        }

        $categories = $normalizer->normalize($categoryRepository->search($term), 'search');
        $products = $normalizer->normalize($productRepository->search($term), 'search');

        return $this->json(array_merge($categories, $products));
    }
}

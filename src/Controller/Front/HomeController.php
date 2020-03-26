<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="home.")
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
    public function index (ModuleRepository $moduleRepository)
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
    public function search (Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $term = $request->query->get('q');
        if ($term === null) {
            return $this->json([], 400);
        }

        $categories = $categoryRepository->search($term);
        $products = $productRepository->search($term);

        return $this->json(array_merge($categories, $products));
    }
}

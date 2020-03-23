<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index (ModuleRepository $moduleRepository)
    {
        $elementsToDisplayInHomepageParameter = $moduleRepository->getParameter('home', 'display');

        $elementsToDisplayInHomepage = $elementsToDisplayInHomepageParameter !== null ?
            $elementsToDisplayInHomepageParameter->getValue() :
            [];

        return $this->render('home/index.html.twig', compact('elementsToDisplayInHomepage'));
    }
}

<?php

namespace App\Twig;

use App\Entity\ModuleParameter;
use App\Repository\ModuleRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public function __construct (ModuleRepository $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    public function getFunctions (): array
    {
        return [
            new TwigFunction('parameter', [$this, 'getParameter']),
        ];
    }

    public function getParameter (string $moduleLabel, string $parameterLabel): ?ModuleParameter
    {
        return $this->moduleRepository->getParameter('home', 'carousel');
    }
}

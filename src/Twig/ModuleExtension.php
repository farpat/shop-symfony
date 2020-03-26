<?php

namespace App\Twig;

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

    /**
     * @param string $moduleLabel
     * @param string $parameterLabel
     *
     * @return array|string|null
     * @throws \Exception
     */
    public function getParameter (string $moduleLabel, string $parameterLabel)
    {
        $value = $this->moduleRepository->getParameter($moduleLabel, $parameterLabel)->getValue();
        return array_key_exists('_value', $value) ? $value['_value'] : $value;
    }
}

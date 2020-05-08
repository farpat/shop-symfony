<?php

namespace App\Twig;

use App\Repository\ModuleRepository;
use App\Services\ModuleService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    private ModuleService $moduleService;

    public function __construct (ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
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
        $value = $this->moduleService->getParameter($moduleLabel, $parameterLabel)->getValue();
        return array_key_exists('_value', $value) ? $value['_value'] : $value;
    }
}

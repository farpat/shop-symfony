<?php

namespace App\Services\Shop\Bank;


use App\Services\ModuleService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StripeServiceFactory
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;
    /**
     * @var ModuleService
     */
    private ModuleService $moduleService;

    public function __construct(ParameterBagInterface $parameterBag, ModuleService $moduleService)
    {
        $this->parameterBag = $parameterBag;
        $this->moduleService = $moduleService;
    }

    public function createStripeService(): StripeService
    {
        return new StripeService(
            $this->parameterBag->get('STRIPE_PUBLIC_KEY'),
            $this->parameterBag->get('STRIPE_SECRET_KEY'),
            $this->moduleService->getParameter('billing', 'currency')->getValue()['code']
        );
    }
}
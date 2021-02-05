<?php

namespace App\Services;


use App\Entity\Module;
use App\Entity\ModuleParameter;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ModuleService
{
    private ModuleRepository       $moduleRepository;
    private EntityManagerInterface $entityManager;
    private CacheInterface         $cache;

    public function __construct(
        ModuleRepository $moduleRepository,
        CacheInterface $cache,
        EntityManagerInterface $entityManager
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function createModule(string $moduleLabel, bool $isActive = false, string $description = null): Module
    {
        $module = (new Module)
            ->setLabel($moduleLabel)
            ->setIsActive($isActive)
            ->setDescription($description);

        $this->entityManager->persist($module);

        return $module;
    }

    public function createParameter(
        string $moduleLabel,
        string $parameterLabel,
        array $value,
        string $description = null
    ): ModuleParameter {
        $module = $this->moduleRepository->findOneBy(['label' => $moduleLabel]);


        if ($module === null) {
            throw new Exception("The module << $moduleLabel >> doesn't exist!");
        }

        $moduleParameter = (new ModuleParameter)
            ->setLabel($parameterLabel)
            ->setValue($value)
            ->setDescription($description)
            ->setModule($module);

        $this->entityManager->persist($moduleParameter);

        $this->cache->delete($cacheKey = $this->getCacheKeyFromObject($moduleParameter));
        return $this->cache->get($cacheKey, fn(ItemInterface $item) => $moduleParameter);
    }

    private function getCacheKeyFromObject(ModuleParameter $moduleParameter): string
    {
        return $this->getCacheKeyFromString((string)$moduleParameter->getModule()->getLabel(), (string)$moduleParameter->getLabel());
    }

    private function getCacheKeyFromString(string $moduleLabel, string $parameterLabel): string
    {
        return "module#getParameter#{$moduleLabel}#{$parameterLabel}";
    }

    public function getParameter(string $moduleLabel, string $parameterLabel): ModuleParameter
    {
        $cacheKey = $this->getCacheKeyFromString($moduleLabel, $parameterLabel);

        return $this->cache->get($cacheKey,
            fn(ItemInterface $item) => $this->moduleRepository->getParameter($moduleLabel, $parameterLabel));
    }
}
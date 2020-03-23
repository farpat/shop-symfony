<?php

namespace App\Repository;

use App\Entity\{Module, ModuleParameter};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    private $cache = [];

    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function createModule (string $moduleLabel, bool $isActive = false, string $description = null): Module
    {
        $module = (new Module)
            ->setLabel($moduleLabel)
            ->setIsActive($isActive)
            ->setDescription($description);

        $this->_em->persist($module);

        return $module;
    }


    public function createParameter (string $moduleLabel, string $parameterLabel, array $value, string $description = null): ModuleParameter
    {
        $module = $this->findOneBy(['label' => $moduleLabel]);


        if ($module === null) {
            throw new \Exception("The module << $moduleLabel >> doesn't exist!");
        }


        $moduleParameter = (new ModuleParameter)
            ->setLabel($parameterLabel)
            ->setValue($value)
            ->setDescription($description)
            ->setModule($module);

        $this->_em->persist($moduleParameter);


        $this->cache[$moduleLabel][$parameterLabel] = $moduleParameter;

        return $moduleParameter;
    }


    public function getParameter (string $moduleLabel, string $parameterLabel): ?ModuleParameter
    {
        if (isset($this->cache[$moduleLabel][$parameterLabel])) {
            return $this->cache[$moduleLabel][$parameterLabel];
        }

        /** @var Module $module */
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $moduleParameter = $queryBuilder
            ->select('p')
            ->from(ModuleParameter::class, 'p')
            ->join('p.module', 'm')
            ->where('p.label = :parameterLabel')
            ->andWhere('m.label = :moduleLabel')
            ->setParameters([
                'moduleLabel'    => $moduleLabel,
                'parameterLabel' => $parameterLabel
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($moduleParameter === null) {
            throw new \Exception("The module parameter << $moduleLabel.$parameterLabel >> doesn't exist!");
        }

        $this->cache[$moduleLabel][$parameterLabel] = $moduleParameter;

        return $moduleParameter;
    }
}

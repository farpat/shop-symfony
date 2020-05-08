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
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function getParameter (string $moduleLabel, string $parameterLabel): ModuleParameter
    {
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

        return $moduleParameter;
    }
}

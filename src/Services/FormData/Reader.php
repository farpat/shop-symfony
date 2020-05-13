<?php

namespace App\Services\FormData;


use Doctrine\Common\Annotations\Reader as SymfonyReader;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;

class Reader
{
    //$cache[classFormData][field] => #annotations
    private $cache = [];
    /**
     * @var SymfonyReader
     */
    private $reader;

    public function __construct(SymfonyReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $classFormData
     * @param string $field
     *
     * @return array
     * @throws ReflectionException
     */
    public function getConstraintAnnotations(string $classFormData, string $field): array
    {
        if (!isset($this->cache[$classFormData][$field])) {
            $property = new ReflectionProperty($classFormData, $field);

            $this->cache[$classFormData][$field] = array_filter(
                $this->reader->getPropertyAnnotations($property),
                fn($annotation) => $this->isConstraint($annotation)
            );
        }
        return $this->cache[$classFormData][$field];
    }

    private function isConstraint($annotation): bool
    {
        return is_subclass_of($annotation, Constraint::class);
    }

}
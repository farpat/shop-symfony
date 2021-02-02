<?php

namespace App\Validator;

use App\Services\Support\Str;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $entityInForm
     * @param Unique $constraint
     */
    public function validate($entityInForm, Constraint $constraint): void
    {
        if (!class_exists($constraint->entity)) {
            throw new \Exception("The entity << $constraint->entity >> does not exist!");
        }

        $repository = $this->entityManager->getRepository($constraint->entity);

        $value = $this->getValue($entityInForm, $constraint->field);

        $entityInDatabase = $repository->findOneBy([$constraint->field => $value]);

        if (!method_exists($entityInForm, 'getId')) {
            throw new \Exception(sprintf('In the entity << %s >>, the method << getId >> does not exist!',
                get_class($entityInForm)));
        }

        if ($entityInDatabase !== null) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->field)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function getValue(object $object, string $field): mixed
    {
        $method = 'get' . Str::getPascalCase($field);
        $callable = [$object, $method];
        if (is_callable($callable)) {
            return call_user_func($callable);
        }

        $class = get_class($object);
        throw new \Exception("The method << $method >> does not exist in << $class >>");
    }
}

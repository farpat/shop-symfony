<?php

namespace App\Validator;

use App\Services\Support\Str;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $formData
     * @param Unique $constraint
     */
    public function validate($formData, Constraint $constraint)
    {
        $repository = $this->entityManager->getRepository($constraint->entity);

        $value = $this->getValue($formData, $constraint->field);

        $dataInDatabase = $repository->findOneBy([
            $constraint->field => $value
        ]);

        if ($dataInDatabase !== null) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->field)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function getValue($object, string $field)
    {
        return call_user_func([$object, 'get' . Str::getPascalCase($field)]);
    }
}

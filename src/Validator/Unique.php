<?php

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Unique extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'The value "{{ value }}" is already used';

    public string $entity;

    public string $field;

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['field', 'entity'];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

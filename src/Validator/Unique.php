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
    public $message = 'The value "{{ value }}" is already used';

    /**
     * @var string
     */
    public $entity;

    /**
     * @var string
     */
    public $field;

    public function getRequiredOptions()
    {
        return ['field', 'entity'];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

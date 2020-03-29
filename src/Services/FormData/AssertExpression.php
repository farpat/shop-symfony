<?php

namespace App\Services\FormData;


use Symfony\Component\Validator\Constraints\Expression;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class AssertExpression extends Expression
{
    public $frontExpression;

    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    public function getRequiredOptions ()
    {
        return array_merge(parent::getRequiredOptions(), ["frontExpression"]);
    }
}
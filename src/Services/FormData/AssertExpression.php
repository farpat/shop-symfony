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
    public $checkFunctionInFrontend;

    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions ()
    {
        return array_merge(parent::getRequiredOptions(), ["checkFunctionInFrontend"]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets ()
    {
        return [self::PROPERTY_CONSTRAINT];
    }
}
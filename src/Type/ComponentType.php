<?php

namespace App\Type;


use App\Services\Annotation\Reader;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComponentType extends TextType
{
    protected Reader $reader;

    public function __construct (Reader $reader)
    {
        $this->reader = $reader;
    }

    protected function getProps (FormInterface $form, array $options): array
    {
        $parentForm = $form->getParent();
        $formName = $parentForm->getName();

        $props = [
            'id'    => "field_{$formName}_{$form->getName()}",
            'value' => $form->getViewData()
        ];

        if ($rules = $this->makeRulesAttribute(get_class($parentForm->getViewData()), $form->getName())) {
            $props['rules'] = $rules;
        }

        if ($label = $this->makeLabelAttribute($options['label'], $form->getName())) {
            $props['label'] = $label;
        }


        if ($attr = $options['attr']) {
            $props['attr'] = $attr;
        }


        return $props;
    }

    protected function makeRulesAttribute (string $class, string $field): ?string
    {
        $attributes = [];

        foreach ($this->reader->getConstraintAnnotations($class, $field) as $constraintAnnotation) {
            $class = get_class($constraintAnnotation);
            switch ($class) {
                case NotBlank::class:
                    $attributes[] = 'Required';
                    break;
                case Email::class:
                    $attributes[] = 'Email';
                    break;
                default:
                    throw new Exception("The annotation << $class >> isn't not already handled");
            }
        }

        if (empty($attributes)) {
            return null;
        }

        return implode('|', $attributes);
    }

    /**
     * @param string|false|null $label
     * @param string $defaultLabel
     *
     * @return string
     */
    protected function makeLabelAttribute ($label, string $defaultLabel): ?string
    {
        if ($label === false) {
            return null;
        }

        if ($label === null) {
            return ucfirst($defaultLabel);
        }

        return (string)$label;
    }
}
<?php

namespace App\Type\Component;


use App\Services\Annotation\Reader;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class TextType extends \Symfony\Component\Form\Extension\Core\Type\TextType
{
    /**
     * @var Reader
     */
    protected $reader;

    public function __construct (Reader $reader)
    {
        $this->reader = $reader;
    }

    public function buildView (FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget_element'] = 'input-component';
        $view->vars['row_attr']['class'] = 'js-form-component';
        $view->vars['row_attr']['data-component'] = 'TextComponent';
        $view->vars['row_attr']['props'] = json_encode($this->getProps($form, $options));

        parent::buildView($view, $form, $options);
    }

    protected function getProps (FormInterface $form, array $options): array
    {
        $parentForm = $form->getParent();
        $formName = $parentForm->getName();

        return $props = [
            'rules' => $this->makeRulesAttribute(get_class($parentForm->getViewData()), $form->getName()),
            'label' => $this->makeLabelAttribute($options['label'], $form->getName()),
            'id'    => "field_{$formName}_{$form->getName()}",
            'type'  => 'text',
        ];
    }

    protected function makeRulesAttribute (string $class, string $field): string
    {
        $attributes = [];

        foreach ($this->reader->getConstraintAnnotations($class, $field) as $constraintAnnotation) {
            $class = get_class($constraintAnnotation);
            switch ($class) {
                case NotBlank::class:
                    $attributes[] = 'required';
                    break;
                case Email::class:
                    $attributes[] = 'email';
                    break;
                default:
                    throw new \Exception("The annotation << $class >> isn't not already handled");
            }
        }

        return implode('|', $attributes);
    }

    /**
     * @param string|false|null $label
     * @param string $defaultLabel
     *
     * @return string
     */
    protected function makeLabelAttribute ($label, string $defaultLabel): string
    {
        if ($label === false) {
            return '';
        }

        if ($label === null) {
            return ucfirst($defaultLabel);
        }

        return (string)$label;
    }
}
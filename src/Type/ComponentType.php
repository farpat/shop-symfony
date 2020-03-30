<?php

namespace App\Type;


use App\Services\FormData\AssertExpression;
use App\Services\FormData\Reader;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ComponentType extends TextType
{
    protected Reader $reader;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct (Reader $reader, TranslatorInterface $translator)
    {
        $this->reader = $reader;
        $this->translator = $translator;
    }

    public function buildView (FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget_element'] = true;
        $view->vars['row_attr']['class'] = 'js-form-component';
        $view->vars['row_attr']['props'] = json_encode($this->getProps($form, $options));

        parent::buildView($view, $form, $options);
    }

    protected function getProps (FormInterface $form, array $options): array
    {
        $parentForm = $form->getParent();
        $formName = $parentForm->getName();
        $errors = $form->getErrors();

        $props = [
            'value' => $form->getViewData(),
            'label' => $this->makeLabelAttribute($options['label'], $form->getName()),
            'name'  => "{$formName}[{$form->getName()}]",
            'id'    => "{$formName}_{$form->getName()}",
            'error' => !isset($errors[0]) ? '' : $errors[0]->getMessage()
        ];

        if ($rules = $this->makeRulesAttribute(get_class($parentForm->getViewData()), $form->getName())) {
            $props['rules'] = $rules;
        }

        if ($attr = $options['attr']) {
            $props['attr'] = $attr;
        }

        if ($help = $options['help']) {
            $props['help'] = $help;
        }

        return $props;
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
            return '';
        }

        if ($label === null) {
            return ucfirst($defaultLabel);
        }

        return (string)$label;
    }

    protected function makeRulesAttribute (string $class, string $field): ?string
    {
        $attributes = [];

        foreach ($this->reader->getConstraintAnnotations($class, $field) as $constraintAnnotation) {
            $class = get_class($constraintAnnotation);
            switch ($class) {
                case NotBlank::class:
                    /** @var  NotBlank $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This value should not be blank.');

                    $attributes[] = "NotBlankßmessage:{$message}";
                    break;
                case IsTrue::class:
                    /** @var  IsTrue $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This value should be true.');

                    $attributes[] = "IsTrueßmessage:{$message}";
                    break;
                case Email::class:
                    /** @var  Email $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This value is not a valid email address.');

                    $attributes[] = "Emailßmessage:{$message}";
                    break;
                case Length::class:
                    /** @var Length $constraintAnnotation */
                    $minMessage = $constraintAnnotation->minMessage !== '' ? $constraintAnnotation->minMessage : $this->translator->trans('The string must contain at least %limit% chars', ['%limit%' => $constraintAnnotation->min]);
                    $maxMessage = $constraintAnnotation->maxMessage !== '' ? $constraintAnnotation->maxMessage : $this->translator->trans('The string must contain at most %limit% chars', ['%limit%' => $constraintAnnotation->max]);

                    $attributes[] = "Lengthßmin:{$constraintAnnotation->min}@max:{$constraintAnnotation->max}@minMessage:{$minMessage}@maxMessage:{$maxMessage}";
                    break;
                case Expression::class:
                    throw new Exception("The annotation << $class >> is not supported. Please, use << " . AssertExpression::class . " >> instead");
                case AssertExpression::class:
                    /** @var AssertExpression $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This string doesn\'t match');

                    $attributes[] = "Expressionßexpression:{$constraintAnnotation->checkFunctionInFrontend}@message:{$message}";
                    break;
                default:
                    throw new Exception("The annotation << $class >> isn't not already handled");
            }
        }

        if (empty($attributes)) {
            return null;
        }

        return implode('²', $attributes);
    }
}
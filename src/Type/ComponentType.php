<?php

namespace App\Type;


use App\Services\FormData\AssertExpression;
use App\Services\FormData\Reader;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
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

    public function __construct(Reader $reader, TranslatorInterface $translator)
    {
        $this->reader = $reader;
        $this->translator = $translator;
    }

    /**
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget_element'] = true;
        $view->vars['row_attr']['class'] = 'js-form-component';
        $view->vars['row_attr']['props'] = json_encode($this->getProps($form, $options), JSON_FORCE_OBJECT);

        parent::buildView($view, $form, $options);
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    protected function getProps(FormInterface $form, array $options): array
    {
        /** @var FormInterface $parentForm */
        $parentForm = $form->getParent();
        $formName = $parentForm->getName();
        $errors = $form->getErrors();

        return [
            'initialValue' => $form->getViewData(),
            'initialError' => $errors->count() > 0 && $errors[0] instanceof FormError ? $errors[0]->getMessage() : '',
            'label'        => $this->makeLabel($options['label'], $form->getName()),
            'name'         => "{$formName}[{$form->getName()}]",
            'id'           => "{$formName}_{$form->getName()}",
            'rules'        => $this->makeRules(get_class($parentForm->getViewData()), $form->getName()),
            'attr'         => !empty($options['attr']) ? $options['attr'] : [],
            'help'         => $options['help'] ?? '',
        ];
    }

    /**
     * @param string|false|null $label
     * @param string $defaultLabel
     *
     * @return string
     */
    protected function makeLabel($label, string $defaultLabel): ?string
    {
        if ($label === false) {
            return '';
        }

        if ($label === null) {
            return ucfirst($defaultLabel);
        }

        return (string)$label;
    }

    /**
     * @return ?array<int, array{type: string, parameters: array}>
     */
    protected function makeRules(string $class, string $field): ?array
    {
        $rules = [];

        foreach ($this->reader->getConstraintAnnotations($class, $field) as $constraintAnnotation) {
            $class = get_class($constraintAnnotation);
            switch ($class) {
                case NotBlank::class:
                    /** @var  NotBlank $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This value should not be blank.');

                    $rules[] = [
                        'type'       => 'NotBlank',
                        'parameters' => [
                            'message' => $message
                        ]
                    ];
                    break;
                case IsTrue::class:
                    /** @var  IsTrue $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This value should be true.');

                    $rules[] = [
                        'type'       => 'IsTrue',
                        'parameters' => [
                            'message' => $message
                        ]
                    ];
                    break;
                case Email::class:
                    /** @var  Email $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This value is not a valid email address.');

                    $rules[] = [
                        'type'       => 'Email',
                        'parameters' => [
                            'message' => $message
                        ]
                    ];
                    break;
                case Length::class:
                    /** @var Length $constraintAnnotation */
                    $minMessage = $constraintAnnotation->minMessage !== '' ? $constraintAnnotation->minMessage : $this->translator->trans('The string must contain at least %limit% chars',
                        ['%limit%' => $constraintAnnotation->min]);
                    $maxMessage = $constraintAnnotation->maxMessage !== '' ? $constraintAnnotation->maxMessage : $this->translator->trans('The string must contain at most %limit% chars',
                        ['%limit%' => $constraintAnnotation->max]);

                    $rules[] = [
                        'type'       => 'Length',
                        'parameters' => [
                            'min'        => $constraintAnnotation->min,
                            'minMessage' => $minMessage,
                            'max'        => $constraintAnnotation->max,
                            'maxMessage' => $maxMessage,
                        ]
                    ];
                    break;
                case AssertExpression::class:
                    /** @var AssertExpression $constraintAnnotation */
                    $message = $constraintAnnotation->message !== '' ? $constraintAnnotation->message : $this->translator->trans('This string doesn\'t match');

                    $rules[] = [
                        'type'       => 'Expression',
                        'parameters' => [
                            'expression' => $constraintAnnotation->checkFunctionInFrontend,
                            'message'    => $message,
                        ]
                    ];
                    break;
                case Expression::class:
                    throw new Exception("The annotation << $class >> is not supported. Please, use << " . AssertExpression::class . " >> instead");
                default:
                    throw new Exception("The annotation << $class >> isn't not already handled");
            }
        }

        return empty($rules) ? null : $rules;
    }
}

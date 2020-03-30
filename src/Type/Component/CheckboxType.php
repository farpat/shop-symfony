<?php

namespace App\Type\Component;


use App\Type\ComponentType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class CheckboxType extends ComponentType
{
    public function buildView (FormView $view, FormInterface $form, array $options)
    {
        $view->vars['row_attr']['data-component'] = 'CheckboxComponent';

        parent::buildView($view, $form, $options);
    }
}
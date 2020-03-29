<?php

namespace App\Type\Component;


use App\Type\ComponentType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class EmailType extends ComponentType
{
    public function buildView (FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget_element'] = 'input-component';
        $view->vars['row_attr']['class'] = 'js-form-component';
        $view->vars['row_attr']['data-component'] = 'EmailComponent';
        $view->vars['row_attr']['props'] = json_encode($this->getProps($form, $options));

        parent::buildView($view, $form, $options);
    }
}
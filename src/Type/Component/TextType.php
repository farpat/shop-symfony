<?php

namespace App\Type\Component;


use App\Type\ComponentType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class TextType extends ComponentType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['row_attr']['data-component'] = 'TextComponent';

        parent::buildView($view, $form, $options);
    }
}
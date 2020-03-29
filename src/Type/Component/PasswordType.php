<?php

namespace App\Type\Component;


use App\Type\ComponentType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends ComponentType
{
    public function configureOptions (OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'with-key' => true
        ]);
    }

    public function buildView (FormView $view, FormInterface $form, array $options)
    {
        $view->vars['row_attr']['data-component'] = 'PasswordComponent';

        parent::buildView($view, $form, $options);
    }

    protected function getProps (FormInterface $form, array $options): array
    {
        $props = parent::getProps($form, $options);
        return array_merge($props, [
            'withKey' => $options['with-key'] ?? true,
        ]);
    }
}
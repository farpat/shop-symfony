<?php

namespace App\Form;

use App\FormData\RegisterFormData;
use App\Type\Component\{CheckboxType, EmailType, PasswordType, TextType};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['autoFocus' => true],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => $this->translator->trans('Enter your e-mail')]
            ])
            ->add('is_agree_with_terms', CheckboxType::class, [
                'label' => $this->translator->trans('Agree terms')
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'attr'  => ['placeholder' => $this->translator->trans('Password')],
                'help'  => $this->translator->trans('The password must contain at least %limit% chars',
                    ['%limit%' => 6])
            ])
            ->add('password_confirmation', PasswordType::class, [
                'label'    => false,
                'attr'     => ['placeholder' => $this->translator->trans('Confirm your password')],
                'with-key' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegisterFormData::class,
            'attr'       => ['id' => 'register_form']
        ]);
    }
}

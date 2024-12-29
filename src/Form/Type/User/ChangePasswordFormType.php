<?php

namespace Essence\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'attr' => [
                    'autocomplete' => 'new-password',
                    'pattern' => ".{8,4096}",
                ],
            ],
            'first_options' => [
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.password.invalid',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'form.password.min',
                        'max' => 4096,
                    ]),
                ],
            ],
            'second_options' => [
                'attr' => [
                    'autocomplete' => 'new-password',
                    'pattern' => ".{8,4096}",
                ],
            ],
            'invalid_message' => 'form.password.invalid_message',
            // Instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

<?php

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType, SubmitType,};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank,};

class ChangePasswordProfileType extends AbstractType
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
                ],
            ],
            'first_options' => [
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.password.invalid_message',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'form.password.min',
                        'max' => 4096,
                    ]),
                ],
            ],
            'second_options' => [
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.password.invalid_message',
                    ]),
                ],
            ],
            'invalid_message' => 'The password fields must match.',
            'mapped' => false,
        ])
            ->add('change', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary rounded-1 shadow-sm',
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

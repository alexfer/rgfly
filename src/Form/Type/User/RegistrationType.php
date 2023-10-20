<?php

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    PasswordType,
    SubmitType,
    EmailType,
};
use Symfony\Component\Validator\Constraints\{
    IsTrue,
    Length,
    NotBlank,
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
                    'attr' => [
                        'min' => User::CONSTRAINTS['email']['min'],
                        'max' => User::CONSTRAINTS['email']['max'],
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.email.not_blank',
                                ]),
                        new Length([
                            'min' => User::CONSTRAINTS['email']['min'],
                            'minMessage' => 'form.first_name.min',
                            'max' => User::CONSTRAINTS['email']['max'],
                            'maxMessage' => 'form.first_name.max',
                                ]),
                    ],
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'form.message.agree_terms',
                                ]),
                    ],
                ])
                ->add('plainPassword', PasswordType::class, [
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.password.not_blank',
                                ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'form.password.min',
                            'max' => 4096,
                            'maxMessage' => 'form.password.max',
                                ]),
                    ],
                ])
                ->add('send', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

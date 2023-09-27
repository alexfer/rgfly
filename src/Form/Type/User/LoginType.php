<?php

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType,
    PasswordType,
    SubmitType,
};
use Symfony\Component\Validator\Constraints\{
    NotBlank,
    Length,
};

class LoginType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', EmailType::class, [
                    'mapped' => false,
                    'attr' => [
                        'min' => User::CONSTRAINTS['email']['min'],
                        'max' => User::CONSTRAINTS['email']['max'],
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.email.not_blank',
                                ]),
                    ],
                ])
                ->add('password', PasswordType::class, [
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
                ->add('login', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token_id' => 'authenticate',
        ]);
    }
}

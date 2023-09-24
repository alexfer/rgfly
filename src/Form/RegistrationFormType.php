<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    PasswordType,
    SubmitType,
    EmailType,
    TextType,
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('first_name', TextType::class, [
                    'attr' => [
                        'min' => User::CONSTRAINTS['first_name']['min'],
                        'max' => User::CONSTRAINTS['first_name']['max'],
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.first_name.not_blank',
                                ]),
                        new Length([
                            'min' => User::CONSTRAINTS['first_name']['min'],
                            'minMessage' => 'form.first_name.min',
                            'max' => User::CONSTRAINTS['first_name']['max'],
                            'maxMessage' => 'form.first_name.max',
                                ]),
                    ],
                ])
                ->add('last_name', TextType::class, [
                    'attr' => [
                        'min' => User::CONSTRAINTS['last_name']['min'],
                        'max' => User::CONSTRAINTS['last_name']['max'],
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.last_name.not_blank',
                                ]),
                        new Length([
                            'min' => User::CONSTRAINTS['last_name']['min'],
                            'minMessage' => 'form.last_name.min',
                            'max' => User::CONSTRAINTS['last_name']['max'],
                            'maxMessage' => 'form.last_name.max',
                                ]),
                    ],
                ])
                ->add('email', EmailType::class, [
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
//                ->add('agreeTerms', CheckboxType::class, [
//                    'mapped' => false,
//                    'constraints' => [
//                        new IsTrue([
//                            'message' => 'You should agree to our terms.',
//                                ]),
//                    ],
//                ])
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
                        'class' => 'btn btn-primary pull-left',
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

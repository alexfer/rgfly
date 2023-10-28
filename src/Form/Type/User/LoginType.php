<?php

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{EmailType, PasswordType, SubmitType,};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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

<?php

namespace App\Form\Type\User;

use App\Entity\{
    User,
    UserDetails,
};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    SubmitType,
    TextType,
};
use Symfony\Component\Validator\Constraints\{
    Length,
    NotBlank,
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
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
                ->add('send', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDetails::class,
        ]);
    }
}

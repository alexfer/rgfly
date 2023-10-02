<?php

namespace App\Form\Type\User;

use App\Entity\{
    UserDetails,
    User,
};
use App\Form\Type\User\UserType;
use Symfony\Component\Validator\Constraints\{
    Length,
    NotBlank,
};
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('first_name', TextType::class, [
                    'mapped' => false,
                    'attr' => [
                        'min' => UserDetails::CONSTRAINTS['first_name']['min'],
                        'max' => UserDetails::CONSTRAINTS['first_name']['max'],
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.first_name.not_blank',
                                ]),
                        new Length([
                            'min' => UserDetails::CONSTRAINTS['first_name']['min'],
                            'minMessage' => 'form.first_name.min',
                            'max' => UserDetails::CONSTRAINTS['first_name']['max'],
                            'maxMessage' => 'form.first_name.max',
                                ]),
                    ],
                ])
                ->add('last_name', TextType::class, [
                    'mapped' => false,
                    'attr' => [
                        'min' => UserDetails::CONSTRAINTS['last_name']['min'],
                        'max' => UserDetails::CONSTRAINTS['last_name']['max'],
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.last_name.not_blank',
                                ]),
                        new Length([
                            'min' => UserDetails::CONSTRAINTS['last_name']['min'],
                            'minMessage' => 'form.last_name.min',
                            'max' => UserDetails::CONSTRAINTS['last_name']['max'],
                            'maxMessage' => 'form.last_name.max',
                                ]),
                    ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getParent(): ?string
    {
        return RegistrationType::class;
    }
}

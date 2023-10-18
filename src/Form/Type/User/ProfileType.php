<?php

namespace App\Form\Type\User;

use App\Entity\UserDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    SubmitType,
    TextType,
    TelType,
    TextareaType,
    FileType,
    DateType,
};
use Symfony\Component\Validator\Constraints\{
    Length,
    NotBlank,
    Regex,
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('first_name', TextType::class, [
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
                ])
                ->add('picture', FileType::class, [
                    'mapped' => false,
                    'attr' => [
                        'accept' => 'image/png, image/jpeg',
                    ],
                    'constraints' => [
                        new Image([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => 'form.picture.not_valid_type',
                                ]),
                    ],
                ])
                ->add('phone', TelType::class, [
                    'attr' => [
                        'pattern' => "[+0-9]+$",
                    ],
                    'constraints' => [
                        new Regex([
                            'pattern' => "/[+0-9]+$/i",
                            'message' => 'form.phone.not_valid',
                                ]),
                    ],
                ])
                ->add('date_birth', DateType::class, [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('about', TextareaType::class, [
                    'attr' => [
                        'min' => UserDetails::CONSTRAINTS['about']['min'],
                        'max' => UserDetails::CONSTRAINTS['about']['max'],
                        'rows' => 6,
                    ],
                    'constraints' => [
                        new Length([
                            'min' => UserDetails::CONSTRAINTS['about']['min'],
                            'minMessage' => 'form.about.min',
                            'max' => UserDetails::CONSTRAINTS['about']['max'],
                            'maxMessage' => 'form.about.max',
                                ]),
                    ],
                ])
                ->add('update', SubmitType::class, [
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

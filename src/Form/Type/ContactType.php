<?php

namespace App\Form\Type;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{EmailType, SubmitType, TelType, TextareaType, TextType,};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'attr' => [
                'min' => 5,
                'max' => 250,
                'pattern' => ".{5,250}",
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'form.title.not_blank',
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'form.title.min',
                    'max' => 250,
                    'maxMessage' => 'form.title.max',
                ]),
            ],
        ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.email.not_blank',
                    ]),
                    new Email(
                        message: 'form.email.not_valid'
                    ),
                ],
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'attr' => [
                    'min' => 8,
                    'max' => 80,
                    'pattern' => "/[+0-9]+$/i",
                ],
                'constraints' => [
                    new Regex(
                        "/[+0-9]+$/i",
                        'form.phone.not_valid',
                    )
                ],
            ])
            ->add('subject', TextType::class, [
                'required' => false,
                'attr' => [
                    'min' => 5,
                    'max' => 250,
                    'pattern' => ".{5,250}",
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'form.subject.min',
                        'max' => 250,
                        'maxMessage' => 'form.subject.max',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'min' => 10,
                    'max' => 65535,
                    'pattern' => ".{10,65535}",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.message.not_blank',
                    ]),
                    new Length([
                        'min' => 100,
                        'minMessage' => 'form.message.min',
                        'max' => 65535,
                        'maxMessage' => 'form.message.max',
                    ]),
                ],
            ])
            ->add('send', SubmitType::class, [
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
            'data_class' => Contact::class,
        ]);
    }
}

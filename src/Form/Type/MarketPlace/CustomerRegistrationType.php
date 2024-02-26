<?php

namespace App\Form\Type\MarketPlace;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\User;
use App\Form\Type\User\RegistrationType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locale;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerRegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'attr' => [
                    'min' => 3,
                    'max' => 200,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.first_name.not_blank',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.first_name.min',
                        'max' => 200,
                        'maxMessage' => 'form.first_name.max',
                    ]),
                ],
            ])
            ->add('last_name', TextType::class, [
                'attr' => [
                    'min' => 2,
                    'max' => 200,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.last_name.not_blank',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'form.last_name.min',
                        'max' => 200,
                        'maxMessage' => 'form.last_name.max',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'min' => 5,
                    'max' => 180,
                ],
                'constraints' => [
//                    new UniqueEntity([
//                        'fields' => ['email'],
//                        'message' => 'email.unique',
//                    ]),
                    new NotBlank([
                        'message' => 'form.email.not_blank',
                    ]),
                    new Email(
                        message: 'form.email.not_valid'
                    ),
                ],
            ])
            ->add('city', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'min' => 3,
                    'max' => 250,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.city.not_blank',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.city.min',
                        'max' => 250,
                        'maxMessage' => 'form.city.max',
                    ]),
                ],
            ])
            ->add('country', ChoiceType::class, [
                'placeholder' => 'form.country.placeholder',
                'label' => 'label.country',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => array_flip(Countries::getNames(Locale::getDefault())),
            ])
            ->add('phone', TelType::class, [
                'attr' => [
                    'min' => 3,
                    'max' => 200,
                    'pattern' => "[+0-9]+$",
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => "/[+0-9]+$/i",
                        'message' => 'form.phone.not_valid',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'min' => 10,
                    'max' => 250,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.address.not_blank',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'form.address.min',
                        'max' => 250,
                        'maxMessage' => 'form.address.max',
                    ]),
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
            'data_class' => MarketCustomer::class,
        ]);
    }

    public function getParent()
    {
        return RegistrationType::class;
    }
}
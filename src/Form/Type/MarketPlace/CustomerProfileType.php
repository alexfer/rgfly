<?php

namespace Inno\Form\Type\MarketPlace;

use Inno\Entity\MarketPlace\StoreCustomer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\{Countries, Locale,};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerProfileType extends AbstractType
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
            ->add('email', EmailType::class, [
                'attr' => [
                    'min' => 5,
                    'max' => 180,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.email.not_blank',
                    ]),
                    new Email(
                        message: 'form.email.not_valid'
                    ),
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
                    'pattern' => "[+0-9]+$",
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => "/[+0-9]+$/i",
                        'message' => 'form.phone.not_valid',
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
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StoreCustomer::class,
        ]);
    }
}
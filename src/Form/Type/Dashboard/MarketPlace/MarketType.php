<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Service\MarketPlace\Currency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class MarketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'min' => 3,
                    'max' => 250,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.name.not_blank',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.name.min',
                        'max' => 250,
                        'maxMessage' => 'form.name.max',
                    ]),
                ],
            ])
            ->add('currency', ChoiceType::class, [
                'placeholder' => 'label.form.market_currency.placeholder',
                'label' => 'label.currency',
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    Currency::USD => Currency::USD,
                    Currency::EUR => Currency::EUR,
                    Currency::UAH => Currency::UAH,

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
            ->add('email', EmailType::class, [
                'attr' => [
                    'min' => 5,
                    'max' => 180,
                ],
                'constraints' => [
                    new Email(
                        message: 'form.email.not_valid'
                    ),
                ],
            ])
            ->add('logo', FileType::class, [
                'mapped' => false,
                'label_attr' => [
                    'name' => 'label.form.market_logo'
                ],
                'attr' => [
                    'accept' => 'image/png, image/jpeg',
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => ini_get('post_max_size'),
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'form.picture.not_valid_type',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'min' => 100,
                    'max' => 65535,
                ],
                'constraints' => [
                    new Length([
                        'min' => 100,
                        'minMessage' => 'form.description.min',
                        'max' => 65535,
                        'maxMessage' => 'form.description.max',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary shadow',
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
            'data_class' => Market::class,
        ]);
    }
}
<?php declare(strict_types=1);

namespace Inno\Form\Type\MarketPlace;

use Inno\Entity\MarketPlace\StoreCustomer;
use Inno\Form\Type\User\RegistrationType;
use Inno\Service\HostApi\HostApiInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, EmailType, TelType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locale;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Email, Length, NotBlank, Regex};

class CustomerRegistrationType extends AbstractType
{

    /**
     * @var array
     */
    private array $location;

    /**
     * @param RequestStack $requestStack
     * @param HostApiInterface $hostApi
     */
    public function __construct(
        RequestStack     $requestStack,
        HostApiInterface $hostApi
    )
    {
        $meta = $hostApi->determine($requestStack->getCurrentRequest()->getClientIp());

        $this->location = [
            'countryCode' => $meta['countryCode'] ?? null,
            'city' => $meta['city'] ?? null,
        ];
    }

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
                    'pattern' => ".{3,200}",
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
                    'pattern' => ".{2,200}",
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
                    'pattern' => ".{5,180}",
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
            ->add('city', TextType::class, [
                'mapped' => false,
                'data' => $this->location['city'] ?: null,
                'attr' => [
                    'min' => 3,
                    'max' => 250,
                    'pattern' => ".{3,250}",
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
                'data' => $this->location['countryCode'] ?: null,
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
                    'pattern' => ".{10,250}",
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
            'data_class' => StoreCustomer::class,
        ]);
    }

    /**
     * @return null|string
     */
    public function getParent(): ?string
    {
        return RegistrationType::class;
    }
}
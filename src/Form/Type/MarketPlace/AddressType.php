<?php

namespace App\Form\Type\MarketPlace;

use App\Entity\MarketPlace\StoreAddress;
use App\Entity\MarketPlace\StoreCustomer;
use App\Service\HostApi\HostApiInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locale;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AddressType extends AbstractType
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
            'countryCode' => $meta['countryCode'],
            'city' => $meta['city'],
        ];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['data'] instanceof StoreCustomer) {
            $options = $options['data']->getStoreAddress();
        } else {
            $options = $options['data'];
        }

        $builder
            ->add('line1', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'min' => 5,
                    'max' => 250,
                    'pattern' => ".{5,250}",
                ],
                'data' => $options?->getLine1(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.address.not_blank',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'form.address.min',
                        'max' => 250,
                        'maxMessage' => 'form.address.max',
                    ]),
                ],
            ])
            ->add('line2', TextType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $options?->getLine2(),
                'attr' => [
                    'min' => 6,
                    'max' => 250,
                    'pattern' => ".{6,250}",
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'form.address.min',
                        'max' => 250,
                        'maxMessage' => 'form.address.max',
                    ]),
                ],
            ])
            ->add('address_country', ChoiceType::class, [
                'mapped' => false,
                'placeholder' => 'form.country.placeholder',
                'label' => 'label.country',
                'required' => true,
                'multiple' => false,
                'data' => $options?->getId() === null ? $this->location['countryCode'] : $options->getCountry(),
                'expanded' => false,
                'choices' => array_flip(Countries::getNames(Locale::getDefault())),
            ])
            ->add('city', TextType::class, [
                'mapped' => false,
                'data' => $options?->getId() === null ? $this->location['city'] : $options->getCity(),
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
                        'minMessage' => 'form.address.min',
                        'max' => 250,
                        'maxMessage' => 'form.address.max',
                    ]),
                ],
            ])
            ->add('region', TextType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $options?->getRegion(),
                'attr' => [
                    'min' => 3,
                    'max' => 250,
                    'pattern' => ".{3,250}",
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.region.min',
                        'max' => 250,
                        'maxMessage' => 'form.region.max',
                    ]),
                ],
            ])
            ->add('postal', TextType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $options?->getPostal(),
                'attr' => [
                    'min' => 3,
                    'max' => 50,
                    'pattern' => ".{3,250}",
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.postal.min',
                        'max' => 50,
                        'maxMessage' => 'form.postal.max',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $options?->getPhone(),
                'attr' => [
                    'pattern' => "[+0-9]+$",
                    'min' => 10,
                    'max' => 50,
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'form.phone.min',
                        'max' => 50,
                        'maxMessage' => 'form.phone.max',
                    ]),
                    new Regex([
                        'pattern' => "/[+0-9]+$/i",
                        'message' => 'form.phone.not_valid',
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
            'data_class' => StoreAddress::class,
        ]);
    }
}


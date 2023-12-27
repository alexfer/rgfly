<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use AllowDynamicProperties;
use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketProduct;
use App\Entity\MarketPlace\MarketProvider;
use App\Service\MarketPlace\MarketTrait;
use App\Service\Navbar;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

#[AllowDynamicProperties] class ProductType extends AbstractType
{

    private null|Market $market;

    public function __construct(private readonly Security $security, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $user = $security->getUser();
        $request = $requestStack->getCurrentRequest();
        $this->market = $em->getRepository(Market::class)->findOneBy(['id' => $request->get('market')]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $providers = $suppliers = $manufacturers = [];

        $marketProviders = $this->market->getMarketProviders()->toArray();
        $marketSuppliers = $this->market->getMarketSuppliers()->toArray();
        $marketSManufacturers = $this->market->getMarketManufacturers()->toArray();

        if ($marketProviders) {
            foreach ($marketProviders as $provider) {
                $providers[$provider->getId()] = $provider->getName();
            }
        }

        if ($marketSuppliers) {
            foreach ($marketSuppliers as $supplier) {
                $suppliers[$supplier->getId()] = $supplier->getName();
            }
        }

        if ($marketSManufacturers) {
            foreach ($marketSManufacturers as $manufacturer) {
                $manufacturers[$manufacturer->getId()] = $manufacturer->getName();
            }
        }

        //dd($options['data']->getMarketProductProvider()->getProvider());

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
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 1000,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.quantity.not_blank',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'form.quantity.min',
                        'max' => 100,
                        'maxMessage' => 'form.quantity.max',
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 100000,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.price.not_blank',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'form.price.min',
                        'max' => 100000,
                        'maxMessage' => 'form.price.max',
                    ]),
                ],
            ])
            ->add('provider', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']->getMarketProductProvider() ? $options['data']->getMarketProductProvider()->getProvider()->getid(): 0,
                //'data' => 0,
                'placeholder' => 'label.form.provider_name',
                'choices' => array_flip($providers),
            ])
            ->add('supplier', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']->getMarketProductSupplier() ? $options['data']->getMarketProductSupplier()->getSupplier()->getid(): 0,
                'placeholder' => 'label.form.supplier_name',
                'choices' => array_flip($suppliers),
            ])
            ->add('manufacturer', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']->getMarketProductManufacturer() ? $options['data']->getMarketProductManufacturer()->getManufacturer()->getid(): 0,
                'placeholder' => 'label.form.manufacturer_name',
                'choices' => array_flip($manufacturers),
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'min' => 10,
                    'max' => 10000,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.description.not_blank',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'form.description.min',
                        'max' => 10000,
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
            'data_class' => MarketProduct::class,
        ]);
    }
}
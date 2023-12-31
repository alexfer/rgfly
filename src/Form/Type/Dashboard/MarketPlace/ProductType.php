<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use AllowDynamicProperties;
use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketProduct;
use App\Entity\MarketPlace\MarketBrand;
use App\Service\MarketPlace\MarketTrait;
use App\Service\Dashboard;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

#[AllowDynamicProperties] class ProductType extends AbstractType
{

    private null|Market $market;

    public function __construct(private readonly Security $security, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $user = $security->getUser();
        $market = $requestStack->getCurrentRequest()->get('market');
        $this->market = $em->getRepository(Market::class)
            ->findOneBy(['id' => $market]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $brands = $suppliers = $manufacturers = [];

        $marketBrands = $this->market->getMarketBrands()->toArray();
        $marketSuppliers = $this->market->getMarketSuppliers()->toArray();
        $marketSManufacturers = $this->market->getMarketManufacturers()->toArray();

        if ($marketBrands) {
            foreach ($marketBrands as $brand) {
                $brands[$brand->getId()] = $brand->getName();
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
                    'max' => 100000,
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
                    'min' => '0.50',
                    'step' => '0.50',
                ],
                'html5' => true,
                'currency' => $this->market->getCurrency() ?? 'USD',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.price.not_blank',
                    ]),
                ],
            ])
            ->add('brand', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']?->getMarketProductBrand()?->getBrand()?->getid(),
                'placeholder' => 'label.form.brand_name',
                'choices' => array_flip($brands),
            ])
            ->add('supplier', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']?->getMarketProductSupplier()?->getSupplier()?->getid(),
                'placeholder' => 'label.form.supplier_name',
                'choices' => array_flip($suppliers),
            ])
            ->add('manufacturer', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']?->getMarketProductManufacturer()?->getManufacturer()?->getid(),
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
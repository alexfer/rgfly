<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use AllowDynamicProperties;
use App\Entity\MarketPlace\{Store, StoreCategory, StoreProduct};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType,
    IntegerType,
    MoneyType,
    RangeType,
    SubmitType,
    TextareaType,
    TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank};

#[AllowDynamicProperties] class ProductType extends AbstractType
{

    /**
     * @var Store|null
     */
    private null|Store $store;

    /**
     * @var EntityRepository
     */
    private EntityRepository $categories;


    /**
     * @param Security $security
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     */
    public function __construct(
        Security               $security,
        RequestStack           $requestStack,
        EntityManagerInterface $em,
    )
    {
        $user = $security->getUser();
        $store = $requestStack->getCurrentRequest()->get('store');
        $this->store = $em->getRepository(Store::class)->find($store);
        $this->categories = $em->getRepository(StoreCategory::class);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $brands = $suppliers = $manufacturers = $categories = [];

        $marketBrands = $this->store->getStoreBrands()->toArray();
        $marketSuppliers = $this->store->getStoreSuppliers()->toArray();
        $marketSManufacturers = $this->store->getStoreManufacturers();
        $marketCategory = $this->categories->findBy([], ['id' => 'asc']);

        if ($marketCategory) {
            foreach ($marketCategory as $category) {
                if ($category->getParent()) {
                    $categories[$category->getParent()->getName()][$category->getName()] = $category->getId();
                }
            }
        }

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
            ->add('short_name', TextType::class, [
                'attr' => [
                    'min' => 3,
                    'max' => 80,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.short_name.not_blank',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.short_name.min',
                        'max' => 80,
                        'maxMessage' => 'form.short_name.max',
                    ]),
                ],
            ])
            ->add('sku', TextType::class, [
                'required' => false,
                'attr' => [
                    'min' => 3,
                    'max' => 80,
                ],
                'constraints' => [
                    new Length([
                        'max' => 80,
                        'maxMessage' => 'form.sku.max',
                    ]),
                ],
            ])
            ->add('name', TextareaType::class, [
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
            ->add('pckg_quantity', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'constraints' => [
                    new Length([
                        'min' => 0,
                        'max' => 100,
                        'maxMessage' => 'form.quantity.max',
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
                        'max' => 1000,
                        'maxMessage' => 'form.quantity.max',
                    ]),
                ],
            ])
            ->add('discount', RangeType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'value' => $options['data']?->getDiscount() ?: 0,
                ],
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => 'form.discount.min',
                        'max' => 100,
                        'maxMessage' => 'form.discount.max',
                    ]),
                ],
            ])
            ->add('fee', MoneyType::class, [
                'attr' => [
                    'min' => '0.00',
                    'step' => '1',
                ],
                'html5' => true,
                'currency' => $this->store->getCurrency() ?: 'USD',
            ])
            ->add('cost', MoneyType::class, [
                'attr' => [
                    'min' => '0.00',
                    'step' => '0.01',
                ],
                'html5' => true,
                'currency' => $this->store->getCurrency() ?: 'USD',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.cost.not_blank',
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'option.category.choice_category',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.category.not_blank',
                    ]),
                ],
                'data' => $options['data']?->getStoreCategoryProducts()?->first() ? $options['data']?->getStoreCategoryProducts()?->first()?->getCategory()?->getId() : null,
                'choices' => $categories,
            ])
            ->add('brand', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']?->getStoreProductBrand()?->getBrand()?->getid(),
                'placeholder' => 'label.form.brand_name',
                'choices' => array_flip($brands),
            ])
            ->add('supplier', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']?->getStoreProductSupplier()?->getSupplier()?->getid(),
                'placeholder' => 'label.form.supplier_name',
                'choices' => array_flip($suppliers),
            ])
            ->add('manufacturer', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']?->getStoreProductManufacturer()?->getManufacturer()?->getid(),
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
            'data_class' => StoreProduct::class,
        ]);
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return AttributeType::class;
    }
}
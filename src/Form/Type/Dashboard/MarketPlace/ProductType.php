<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use AllowDynamicProperties;
use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;

#[AllowDynamicProperties] class ProductType extends AbstractType
{

    /**
     * @var Market|null
     */
    private null|Market $market;

    /**
     * @var EntityRepository
     */
    private EntityRepository $categories, $productCategories;


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
        $market = $requestStack->getCurrentRequest()->get('market');
        $this->market = $em->getRepository(Market::class)
            ->findOneBy(['id' => $market]);
        $this->categories = $em->getRepository(MarketCategory::class);
        $this->productCategories = $em->getRepository(MarketCategoryProduct::class);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $brands = $suppliers = $manufacturers = $categories = $productCategories = [];

        $marketBrands = $this->market->getMarketBrands()->toArray();
        $marketSuppliers = $this->market->getMarketSuppliers()->toArray();
        $marketSManufacturers = $this->market->getMarketManufacturers();
        $marketCategory = $this->categories->findBy([], ['id' => 'asc']);
        $marketProductCategory = $this->productCategories->findBy(['product' => $options['data']->getId()]);

        if($marketProductCategory) {
            foreach ($marketProductCategory as $productCategory) {
                $productCategories[$productCategory->getCategory()->getId()] = $productCategory->getCategory()->getName();
            }
        }
        if ($marketCategory) {
            foreach ($marketCategory as $category) {
                if($category->getParent()) {
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
                        'max' => 10000,
                        'maxMessage' => 'form.quantity.max',
                    ]),
                ],
            ])
            ->add('cost', MoneyType::class, [
                'attr' => [
                    'min' => '0.10',
                    'step' => '0.10',
                ],
                'html5' => true,
                'currency' => $this->market->getCurrency() ?? 'EUR',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.cost.not_blank',
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'data' => array_keys($productCategories),
                'choices' => $categories,
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
    public
    function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MarketProduct::class,
        ]);
    }
}
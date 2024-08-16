<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\StoreProductDiscount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', MoneyType::class, [
                'mapped' => false,
                'label' => 'label.form.discount',
                'required' => false,
                'html5' => true,
                'data' => $options['data']->getStoreProductDiscount()?->getValue(),
                'currency' => $options['data']?->getStore()?->getCurrency() ?: 'USD',
                'attr' => [
                    'min' => '0',
                    'step' => '1',
                ],
            ])
            ->add('unit', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Unit',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'placeholder.choose.unit',
                'data' => $options['data']->getStoreProductDiscount()?->getUnit(),
                'choices' => [
                    ucfirst(StoreProductDiscount::UNIT_PERCENTAGE) => 'percentage',
                    ucfirst(StoreProductDiscount::UNIT_STOCK) => 'stock',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StoreProductDiscount::class,
        ]);
    }

}

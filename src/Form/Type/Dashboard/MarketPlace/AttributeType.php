<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\StoreProductAttribute;
use App\Helper\MarketPlace\StoreAttributeValues;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, SubmitType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('size', ChoiceType::class, [
                'label' => 'label.size',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [StoreAttributeValues::ATTRIBUTES['Size']],
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'label.color',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [StoreAttributeValues::ATTRIBUTES['Color']],
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
            'data_class' => StoreProductAttribute::class,
        ]);
    }
}
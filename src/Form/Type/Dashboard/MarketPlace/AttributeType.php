<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\MarketProductAttribute;
use App\Helper\MarketPlace\MarketAttributeValues;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                'mapped' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'choices' => [MarketAttributeValues::ATTRIBUTES['Size']],
            ])
            ->add('color', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'choices' => [MarketAttributeValues::ATTRIBUTES['Color']],
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
            'data_class' => MarketProductAttribute::class,
        ]);
    }
}
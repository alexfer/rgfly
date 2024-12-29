<?php declare(strict_types=1);

namespace Essence\Form\Type\Dashboard\MarketPlace;

use Essence\Entity\MarketPlace\StorePaymentGateway;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentGatewayType extends AbstractType
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
                    'maxlength' => 255,
                ],
            ])
            ->add('summary', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('handlerText', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('logo', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StorePaymentGateway::class,
        ]);
    }
}

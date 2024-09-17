<?php declare(strict_types=1);

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\StorePaymentGateway;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentGatewayType extends AbstractType
{
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
            ->add('icon', TextType::class, [
                'attr' => [
                    'maxlength' => 50,
                ]
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
                'data' => false,
            ])
            ->add('save', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StorePaymentGateway::class,
        ]);
    }
}

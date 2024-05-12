<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCoupon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CouponType extends AbstractType
{
    /**
     * @var Market|null
     */
    private null|Market $market;

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
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.form.name',
                'attr' => [
                    'maxlength' => 255,
                    'minlength' => 6,
                    'pattern' => ".{6,255}",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.name.not_blank',
                    ]),
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'label.form.code',
                'attr' => [
                    'readonly' => true,
                ],
                'data' => $options['data']->getCode() ?: strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6)),
            ])
            ->add('startedAt', DateTimeType::class, [
                'date_label' => 'label.form.startedAt',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.date.not_blank',
                    ]),
                ],
            ])
            ->add('expiredAt', DateTimeType::class, [
                'date_label' => 'label.form.expiredAt',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.date.not_blank',
                    ]),
                ],
            ])
            ->add('discount', RangeType::class, [
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'pattern' => "[1-9]{100}",
                    'value' => $options['data']?->getDiscount() ?: 0,
                ],
                'constraints' => [
                    new Positive(),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'form.discount.min',
                        'max' => 100,
                        'maxMessage' => 'form.discount.max',
                    ]),
                ],
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => '0.01',
                ],
                'html5' => true,
            ])
            ->add('event', ChoiceType::class, [
                'label' => 'label.form.event',
                'choices' => [
                    'Start' => 1,
                    'End' => 0,
                ],
                'expanded' => true,
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
            'data_class' => MarketCoupon::class,
        ]);
    }
}
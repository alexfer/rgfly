<?php declare(strict_types=1);

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\StoreCarrier;
use Liip\ImagineBundle\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class CarrierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('logo', ImageType::class, [
            'mapped' => false,
            'required' => false,
            'label_attr' => [
                'name' => 'label.form.carrier_logo'
            ],
            'attr' => [
                'accept' => 'image/png, image/jpeg, image/webp',
            ],
            'constraints' => [
                new Image([
                    'maxSize' => ini_get('post_max_size'),
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    'mimeTypesMessage' => 'form.picture.not_valid_type',
                ]),
            ],
        ])
            ->add('linkUrl', UrlType::class, [
                'default_protocol' => 'https',
                'attr' => [
                    'placeholder' => 'https://',
                ],
                'constraints' => [
                    new Regex(
                        '/(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i',
                        'form.url.not_valid',
                    )
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'min' => 10,
                    'max' => 255,
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'form.description.min',
                        'max' => 255,
                        'maxMessage' => 'form.description.max',
                    ]),
                ],
            ])
            ->add('shippingAmount', HiddenType::class, [
                'data' => 0,
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label_attr' => [
                    'name' => 'label.enabled',
                ],
                'data' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StoreCarrier::class
        ]);
    }
}

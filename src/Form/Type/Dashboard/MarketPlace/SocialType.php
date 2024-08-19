<?php

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\StoreSocial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, UrlType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SocialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sourceName', ChoiceType::class, [
            'label' => 'label.source',
            'mapped' => false,
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => StoreSocial::NAME,
        ]);

        foreach (StoreSocial::NAME as $name) {
            $builder->add($name, UrlType::class, [
                'mapped' => false,
                'label' => 'label.social.' . $name,
                'required' => true,
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
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StoreSocial::class,
        ]);
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return OptionsType::class;
    }
}
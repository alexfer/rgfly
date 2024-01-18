<?php

namespace App\Form\Type;

use App\Entity\Faq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, SubmitType, TextType, TextareaType,};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank,};

class FaqType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
            'attr' => [
                'min' => 10,
                'max' => 250,
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'form.title.not_blank',
                ]),
                new Length([
                    'min' => 10,
                    'minMessage' => 'form.title.min',
                    'max' => 250,
                    'maxMessage' => 'form.title.max',
                ]),
            ],
        ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'min' => 100,
                    'max' => 65535,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.content.not_blank',
                    ]),
                    new Length([
                        'min' => 100,
                        'minMessage' => 'form.content.min',
                        'max' => 65535,
                        'maxMessage' => 'form.content.max',
                    ]),
                ],
            ])
            ->add('visible', CheckboxType::class, [
                'mapped' => true,
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
            'data_class' => Faq::class,
        ]);
    }
}

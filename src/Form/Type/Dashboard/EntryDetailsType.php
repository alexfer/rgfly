<?php

namespace App\Form\Type\Dashboard;

use App\Entity\Entry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{SubmitType, TextareaType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length, NotBlank};

class EntryDetailsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
            'mapped' => false,
            'data' => $options['data']?->getEntryDetails()?->getTitle(),
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
            ->add('short_content', TextareaType::class, [
                'mapped' => false,
                'data' => $options['data']?->getEntryDetails()?->getShortContent(),
                'attr' => [
                    'min' => 50,
                    'max' => 500,
                ],
                'constraints' => [
                    new Length([
                        'min' => 50,
                        'minMessage' => 'form.short_content.min',
                        'max' => 500,
                        'maxMessage' => 'form.short_content.max',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
//                'config' => [
//                    'toolbar' => 'full'
//                ],
                'mapped' => false,
                'data' => $options['data']?->getEntryDetails()?->getContent(),
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
            ->add('next', SubmitType::class, [
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
            'data_class' => Entry::class,
        ]);
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return EntryType::class;
    }
}

<?php

namespace App\Form\Type;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType,
    TelType,
    TextareaType,
    TextType,
    SubmitType,
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
                    'attr' => [
                        'min' => Contact::CONSTRAINTS['name']['min'],
                        'max' => Contact::CONSTRAINTS['name']['max'],
                    ],
                ])
                ->add('email', EmailType::class, [])
                ->add('phone', TelType::class, [])
                ->add('subject', TextType::class, [
                    'attr' => [
                        'min' => Contact::CONSTRAINTS['subject']['min'],
                        'max' => Contact::CONSTRAINTS['subject']['max'],
                    ],
                ])
                ->add('message', TextareaType::class, [
                    'attr' => [
                        'min' => Contact::CONSTRAINTS['message']['min'],
                        'max' => Contact::CONSTRAINTS['message']['max'],
                    ],
                ])
                ->add('send', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary pull-left',
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}

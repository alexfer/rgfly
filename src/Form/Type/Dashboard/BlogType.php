<?php

namespace Inno\Form\Type\Dashboard;

use Inno\Entity\Entry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $builder->add('categories', ChoiceType::class, [
//            'mapped' => false,
//            'choice_attr' => ChoiceList::attr($this, function (?Category $category): array {
//                return $category ? [$category->getId() => $category->getName()] : [];
//            }),
//        ]);
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
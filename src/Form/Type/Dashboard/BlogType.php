<?php

namespace App\Form\Type\Dashboard;

use App\Entity\Category;
use App\Entity\Entry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('categories', ChoiceType::class, [
            'mapped' => false,
            'choice_attr' => ChoiceList::attr($this, function (?Category $category): array {
                return $category ? [$category->getId() => $category->getName()] : [];
            }),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }

    public function getParent(): ?string
    {
        return EntryType::class;
    }
}
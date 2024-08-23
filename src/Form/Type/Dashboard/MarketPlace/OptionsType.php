<?php declare(strict_types=1);

namespace App\Form\Type\Dashboard\MarketPlace;

use App\Entity\MarketPlace\StoreOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('backupSchedule', CheckboxType::class, [
            'label' => 'label.schedule.backup',
            'data' => $options['data']->getStoreOptions() && $options['data']->getStoreOptions()->getBackupSchedule(),
            'mapped' => false,
            'required' => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StoreOptions::class,
        ]);
    }
}
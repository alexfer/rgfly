<?php

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacebookType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', 'text')
                ->add('email', 'text', array(
                    'disabled' => true
                ))
                ->add('plainPassword', 'repeated', array(
                    'mapped' => false, // allows this to not be a real property on User
                    'type' => 'password',
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Password again'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}

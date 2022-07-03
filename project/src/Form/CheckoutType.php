<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Barbecue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('adress')
            ->add('phone_number')
            ->add('start_date', DateType::class, array(
                'required' => true,
                'mapped' => false
            ))
            ->add('end_date', DateType::class, array(
                'required' => true,
                'mapped' => false
            ))
            ->add('remark', TextareaType::class, array(
                'required' => false,
                'data' => 'abcdef',
                'mapped' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}

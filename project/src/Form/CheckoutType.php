<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Barbecue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('adress')
            ->add('delivery', ChoiceType::class, [
                'choices'  => [
                    'Yes' => true,
                    'No' => false
                ],
                'required' => true,
                'label' => 'afleveren',
                'mapped' => false
                ])
            ->add('phone_number', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex('/^0[0-9]{9}$/')
                ],
            ])
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
                'data' => '',
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

<?php

namespace App\Vendor\Form;

use App\Vendor\Entity\VendorPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VendorPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['autofocus' => true],
                'label' => 'vendor.plan.fields.name',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'vendor.plan.fields.price',
                'divisor' => 100,
                'scale' => 2,
                'currency' => null,
            ])
            ->add('currency', null, [
                'label' => 'vendor.plan.fields.currency',
            ])
            ->add('duration', DateIntervalType::class, [
                'label' => 'vendor.plan.fields.duration',
                'input' => 'dateinterval',
                'with_years' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VendorPlan::class,
        ]);
    }
}

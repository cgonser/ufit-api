<?php

namespace App\Vendor\Form;

use App\Vendor\Entity\Vendor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VendorSignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'vendor.fields.name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'vendor.fields.email',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'vendor.fields.password',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vendor::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('oldPassword', PasswordType::class, [
            'required' => true,
            'label' => 'Escriba su contraseña actual',
            ])
            
        ->add('newPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Contraseña no coincide',
            'first_options'  => ['label' => 'Escriba su nueva contraseña'],
            'second_options' => ['label' => 'Vuelva a escribir su nueva contraseña']    
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }

    public function getName()
    {
        return 'change_passwd';
    }

}

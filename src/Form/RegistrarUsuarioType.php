<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class RegistrarUsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, array(
            'label' => 'Correo electrónico', 
            'required' => true,
            'attr' => array('maxlenght' => 64)))

        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Error al introducir contraseña',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Contraseña'],
            'second_options' => ['label' => 'Repita la contraseña'],])

        ->add('nombre', TextType::class, array(
            'label' => 'Nombre', 
            'required' => true,
            'attr' => array('maxlenght' => 20)))

        ->add('apellidos', TextType::class, array(
            'label' => 'Apellidos', 
            'required' => true,
            'attr' => array('maxlenght' => 64)))

        ->add('telefono', IntegerType::class, array(
            'label' => 'Teléfono', 
            'required' => true,
            'attr' => array('maxlenght' => 9)))
    
        ->add('roles', ChoiceType::class, [
            'label' => 'Rol del Usuario',
            'required' => true,
            'multiple' => false,
            'choices' => [
                'Socio' => 'ROLE_SOCIO',
                'Consultor' => 'ROLE_CONSULTOR',
                'Editor' => 'ROLE_EDITOR',
                'Administrador' => 'ROLE_ADMINISTRADOR',],
            ])
            ->add('Confirmar', type: SubmitType::class)
        ;

        //Conversión para el campo de elección de roles
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    //Transformar el array en string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    //Volver a transformar el string en array
                    return [$rolesString];
                }
        ));

}

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Usuario::class,
    ]);
}
}
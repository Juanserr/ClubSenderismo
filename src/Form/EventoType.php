<?php

namespace App\Form;

use App\Entity\Evento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, array(
                'label' => 'Nombre',
                'required' => true,
                'attr' => array('maxlength' => 64)))
            ->add('lugar', TextType::class, array(
                'label' => 'Lugar',
                'required' => true,
                'attr' => array('maxlength' => 64)))
            ->add('fecha', DateType::class, array(
                'label' => 'Fecha',
                'widget' => 'choice',
                'required' => true))
            ->add('hora', TimeType::class, array(
                'label'  => 'Hora',
                'input'  => 'datetime',
                'required' => true,
                'widget' => 'choice'))
            ->add('descripcion', TextareaType::class, array(
                'label' => 'Descripción',
                'required' => true,
                'attr' => array('maxlength' => 500)))    
            
            ->add('Guardar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evento::class,
        ]);
    }
}

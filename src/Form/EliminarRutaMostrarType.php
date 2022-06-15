<?php

namespace App\Form;

use App\Entity\Ruta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EliminarRutaMostrarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', TextType::class, array(
            'label' => 'Nombre',
            'required' => true,
            'disabled' => true,
            'attr' => array('maxlength' => 64)))
        
        ->add('lugar_inicio', TextType::class, array(
            'label' => 'Lugar de Inicio',
            'required' => true,
            'disabled' => true,
            'attr' => array('maxlength' => 64)))

        ->add('lugar_fin', TextType::class, array(
            'label' => 'Lugar de Fin',
            'required' => false,
            'disabled' => true,
            'attr' => array('maxlength' => 64)))

        ->add('distancia', NumberType::class, array(
            'label' => 'Distancia (metros)', 
            'required' => true,
            'disabled' => true,
            'attr' => array('maxlenght' => 15),
            'help' => 'Ej: 5000'))

        ->add('desnivel_acumulado', NumberType::class, array(
            'label' => 'Desnivel Acumulado (metros)', 
            'required' => true,
            'disabled' => true,
            'attr' => array('maxlenght' => 10),
            'help' => 'Ej: 2000'))

        ->add('duracion', TextType::class, array(
            'label' => 'Duración',
            'required' => true,
            'disabled' => true,
            'attr' => array('maxlength' => 64),
            'help' => 'Ej: 1 hora y 30 minutos'))

        ->add('fecha', DateType::class, array(
            'label' => 'Fecha',
            'widget' => 'choice',
            'disabled' => true,
            'required' => true))

        ->add('hora_inicio', TimeType::class, array(
            'label'  => 'Hora de inicio',
            'input'  => 'datetime',
            'disabled' => true,
            'required' => true,
            'widget' => 'choice'))

        ->add('hora_fin', TimeType::class, array(
            'label'  => 'Hora de fin',
            'input'  => 'datetime',
            'disabled' => true,
            'required' => true,
            'widget' => 'choice'))

        ->add('recorrido', TextareaType::class, array(
            'label' => 'Recorrido',
            'required' => true,
            'disabled' => true,
            'attr' => array('maxlength' => 64)))

        ->add('dificultad', ChoiceType::class, array(
            'label' => 'Dificultad',
            'choices'  => [
                'Baja' => 'Baja',
                'Media' => 'Media',
                'Alta' => 'Alta',
                'Muy alta' => 'Muy alta',
            ],
            'required' => true,
            'disabled' => true))
            ->add('Confirmar', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ruta::class,
        ]);
    }
}

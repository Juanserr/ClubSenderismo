<?php

namespace App\Form;

use App\Entity\RutaConInscripcion;
use App\Entity\Ruta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RutaConInscripcionType extends DatosRutaType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plazas', IntegerType::class, array(
                'label' => 'Plazas',
                'required' => true,
                'attr' => array('maxlength' => 6)))
            
            ->add('fecha_socio', DateType::class, array(
                'label' => 'Fecha de inscripción para socios',
                'widget' => 'choice',
                'required' => true))

            ->add('fecha_nosocio', DateType::class, array(
                'label' => 'Fecha de inscripción para personas que no son socias al club',
                'widget' => 'choice',
                'required' => true))
            //->add('ruta')
            ->add('Confirmar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RutaConInscripcion::class,
        ]);
    }

    public function getName()
    {
        return 'RutaInscripcion';
    }
}

<?php

namespace App\Form;

use App\Entity\SocioMaterialdeportivo;
use App\Form\DatosRutaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocioMaterialdeportivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('material', MaterialDeportivoType::class)
            //->add('estado')
            //->add('fecha_solicitud')
            //->add('id_usuario')
            //->add('id_material')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocioMaterialdeportivo::class,
        ]);
    }
}

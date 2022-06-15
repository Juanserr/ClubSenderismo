<?php

namespace App\Form;

use App\Entity\MaterialDeportivo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialDeportivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, array(
                'label' => 'Nombre',
                'required' => true,
                'attr' => array('maxlength' => 64)))
            ->add('marca', TextType::class, array(
                'label' => 'Marca',
                'required' => true,
                'attr' => array('maxlength' => 64)))
            ->add('talla', ChoiceType::class, array(
                'label' => 'Talla',
                'choices'  => [
                    'XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL',  'XXXL' => 'XXXL',
                    '36' => '36', '38' => '38', '40' => '40', '42' => '42', '44' => '44','46' => '46', '48' => '48', '50' => '50', '52' => '52',
                ],
                'required' => true))
            ->add('imagen_prenda', FileType::class,
                ['label' => 'Seleccione una imagen (Archivo PNG)', 
                'mapped' => false, 
                'required' => true])
            ->add('sexo', ChoiceType::class, array(
                'label' => 'Sexo',
                'choices'  => [
                    'Hombre' => 'Hombre',
                    'Mujer' => 'Mujer',
                ],
                'required' => true))
            ->add('color', TextType::class, array(
                'label' => 'Color',
                'required' => true,
                'attr' => array('maxlength' => 64)))
            ->add('tela', TextType::class, array(
                'label' => 'Tela',
                'required' => true,
                'attr' => array('maxlength' => 64)))
            //->add('fecha_oferta')
            ->add('Confirmar', type: SubmitType::class)
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaterialDeportivo::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\MaterialDeportivo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatosMaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, array(
                'label' => 'Nombre',
                'disabled' => true))
            ->add('marca', TextType::class, array(
                'label' => 'Marca',
                'disabled' => true))
            ->add('talla', ChoiceType::class, array(
                'label' => 'Talla',
                'choices'  => [
                    'XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL',  'XXXL' => 'XXXL',
                    '36' => '36', '38' => '38', '40' => '40', '42' => '42', '44' => '44','46' => '46', '48' => '48', '50' => '50', '52' => '52',
                ],
                'disabled' => true))
            ->add('sexo', ChoiceType::class, array(
                'label' => 'Sexo',
                'choices'  => [
                    'Hombre' => 'Hombre',
                    'Mujer' => 'Mujer',
                ],
                'disabled' => true))
            ->add('color', TextType::class, array(
                'label' => 'Color',
                'disabled' => true))
            ->add('tela', TextType::class, array(
                'label' => 'Tela',
                'disabled' => true))
            ->add('Confirmar', SubmitType::class)
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaterialDeportivo::class,
        ]);
    }
}

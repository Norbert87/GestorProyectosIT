<?php

namespace App\Form;

use App\Entity\Caracteristica;
use App\Entity\TipoAplicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TipoAplicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('nombre_en',TextType::class,['mapped'=>false,'label'=>'Nombre(English)','data'=>$options['nombre_en']])
            ->add('preciobase',NumberType::class,['required'=>true,'html5'=>true,'attr'=>['step' => ".01"]])
        ;

        $builder->get('preciobase')->addModelTransformer(new CallbackTransformer(
            function ($preciofinalfloat) {
                return (float)  $preciofinalfloat;
            },
            function ($preciofinalstring) {
                return (string)($preciofinalstring);
            }
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TipoAplicacion::class,
            'nombre_en' => ''
        ]);
    }
}

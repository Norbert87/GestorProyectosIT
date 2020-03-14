<?php

namespace App\Form;

use App\Entity\Proyecto;
use App\Entity\Tarea;
use App\Entity\TipoAplicacion;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TareaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcionCorta')
            ->add('descripcionLarga')
            ->add('notas')
            ->add('tecnico', EntityType::class, [
                'label'=> 'Técnico',
                'class' => Usuario::class,
                'choices' => [
                    $options['proyecto']->getTecnicos()->toArray()
                ],
                'choice_label' => 'email'
            ]);
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tarea::class,
            'proyecto' => new Proyecto()
        ]);
    }
}

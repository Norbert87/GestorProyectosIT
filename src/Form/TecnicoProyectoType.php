<?php


namespace App\Form;

use App\Entity\Proyecto;
use App\Entity\Usuario;
use App\Repository\ProyectoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TecnicoProyectoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('tecnico', ChoiceType::class, [
            // looks for choices from this entity
            'choices' => [
                    $options['usuarios']
                ],
            'mapped'=> false,
            'label' => false,


        ]);
        $builder->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
            'label'=>'Añadir',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'usuarios'=>[]
        ]);
    }
}
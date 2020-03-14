<?php

namespace App\Form;


use App\Entity\Presupuesto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\CallbackTransformer;


class PresupuestoAprobarType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('preciofinal',NumberType::class,['required'=>true,'html5'=>true,'attr'=>['step' => ".01"]])
                ->add('fechaentrega',DateType::class,['required'=>true]);

        $builder->get('preciofinal')->addModelTransformer(new CallbackTransformer(
            function ($preciofinalfloat) {
                // transform the array to a string
                return (float)  $preciofinalfloat;
            },
            function ($preciofinalstring) {
                // transform the string back to an array
                return (string)($preciofinalstring);
            }
        ));

        $builder->add('aprobar', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
            'label'=>'Aprobar',
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Presupuesto::class,
        ]);
    }

}

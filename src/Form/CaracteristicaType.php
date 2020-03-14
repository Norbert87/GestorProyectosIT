<?php

namespace App\Form;

use App\Entity\Caracteristica;
use App\Entity\TipoAplicacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaracteristicaType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tipos = $this->entityManager->getRepository(TipoAplicacion::class)->findby(['activo'=>true]);
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

        $builder->add('tipoaplicacion', EntityType::class, [
            // looks for choices from this entity
            'class' => TipoAplicacion::class,
            'label' => 'Tipo aplicación',

            // uses the User.username property as the visible option string
            'choice_label' => 'nombre',
            'choices' => $tipos

            // used to render a select box, check boxes or radios
            // 'multiple' => true,
            // 'expanded' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Caracteristica::class,
            'nombre_en' => ''
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Caracteristica;
use App\Entity\Presupuesto;
use App\Repository\ProyectoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PresupuestoStep3Type extends AbstractType
{

    private $entityManager;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre',TextType::class,['required'=>true,'label'=>$this->translator->trans('Nombre',[],'public')])
            ->add('email',TextType::class,['required'=>true,'label'=>$this->translator->trans('Email',[],'public')])
            ->add('telefono',TextType::class,['required'=>true,'label'=>$this->translator->trans('Telefono',[],'public')]);

        $builder->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
            'label'=>$this->translator->trans('Guardar',[],'public'),
        ]);
//        $builder->addEventListener(
//            FormEvents::PRE_SUBMIT,
//            [$this, 'onPreSubmit']
//        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Presupuesto::class,
        ]);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        if($data['step']=='1') {

            //$form->add('otras');
            $data['step'] = '2';


//            $tipos = $this->entityManager->getRepository(TipoAplicacion::class)->findById($data['tipos']);
//            $presupuestostipo = [];
//            foreach($tipos as $t)
//            {
//                $pretipo = new PresupuestoTipoAplicacion();
//                $pretipo->setTipoaplicacion($t);
//                $presupuestostipo[] = $pretipo;
//            }

//            $form->remove('tipos');
//            $form->add('tipoaplicacion', ChoiceType::class, [
//                'label'=> 'Tipos',
//                'disabled'=> true,
//                'expanded' => true,
//                'empty_data' => true,
//                'choices' => [
//                    $presupuestostipo
//                ],
//                'choice_label' => function(PresupuestoTipoAplicacion $tipo, $key, $value) {
//                    return $tipo->getTipoaplicacion()->getNombre();
//                }
//                ]);
//            $data['tipoaplicacion']= $data['tipos'];
        }

//        $form->remove('tipoaplicacion');
        //$dataform = $form->getData();
//        $form->add('tipoaplicacion', EntityType::class, [
//            // looks for choices from this entity
//            'class' => TipoAplicacion::class,
//
//            // uses the User.username property as the visible option string
//            'choice_label' => 'nombre',
//            'label' => 'Tipos de aplicacion',
//
//            // used to render a select box, check boxes or radios
//            'multiple' => true,
//            'expanded' => true,
//            'disabled' => true,
////            'mapped' => false,
//        ]);

        //$form->setData($dataform);


        $caracs = $this->entityManager->getRepository(Caracteristica::class)->findByTipoaplicacion($data['tipoaplicacion']);


        $form->add('caracteristicas', EntityType::class, [
            'label'=> 'Caracteristicas',
            'class' => Caracteristica::class,
            'expanded' => true,
            'multiple' => true,
            'mapped' => true,
            'choices' => [
                $caracs
            ],
            'choice_label' => function(Caracteristica $carac, $key, $value) {
                return $carac->getNombre().' ('.$carac->getTipoaplicacion()->getNombre().')';
            }
            ]);
        $form->add('nombre')
            ->add('email')
            ->add('telefono')
            ->add('otras');
        $form->remove('save');
        $form->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
            'label'=>'Guardar',
        ]);
        $event->setData($data);
        //$event->stopPropagation();

        if($data['step']=='2') {

        }
    }
}

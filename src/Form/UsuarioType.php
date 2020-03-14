<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class,[
                'label'=> 'Rol',
                'mapped'=>true,
                'choices'  => [
                    'Admnistrador' => 'ROLE_ADMINISTRADOR',
                    'JefeProyecto' => 'ROLE_JEFEPROYECTO',
                    'Comercial' => 'ROLE_COMERCIAL',
                    'Técnico' => 'ROLE_TECNICO'

                ]])
            ->add('locale', ChoiceType::class,[
                'label'=> 'Idioma',
                'choices'  => [
                    'Español' => 'es',
                    'English' => 'en'
                ]]);
        if($options['editar'])
        {
            $builder->add('password',PasswordType::class,['label'=>'Cambiar contraseña','required'=>false]);
            $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']);
        }
        else
        {
            $builder->add('password',PasswordType::class,[]);
        }
        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function ($array) {
                // transform the array to a string
                return $array[0];
            },
            function ($string) {
                // transform the string back to an array
                return [$string];
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'editar' =>false
        ]);
    }
    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $data->setPassword("");
        $event->setData($data);
    }
}

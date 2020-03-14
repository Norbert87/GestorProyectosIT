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
use Symfony\Component\Security\Core\Security;

class PerfilType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $builder
            ->add('locale', ChoiceType::class,[
                'label'=> 'Idioma',

                'choices'  => [
                    'Español' => 'es',
                    'English' => 'en'
                ],
                'data' => $user->getLocale(),
            ]);

        $builder->add('password',PasswordType::class,['label'=>'Cambiar contraseña','required'=>false]);
        $builder->addEventListener(
        FormEvents::PRE_SET_DATA,
        [$this, 'onPreSetData']);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
    public function onPreSetData(FormEvent $event)
    {
//        $data = $event->getData();
//        $data->setPassword("");
//        $event->setData($data);
    }
}

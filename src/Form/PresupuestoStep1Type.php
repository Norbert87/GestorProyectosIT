<?php


namespace App\Form;


use App\Entity\Caracteristica;
use App\Entity\TipoAplicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Contracts\Translation\TranslatorInterface;

class PresupuestoStep1Type extends AbstractType
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

        $tipos = $this->entityManager->getRepository(TipoAplicacion::class)->findby(['activo'=>true]);

        $builder->add('tipoaplicacion', EntityType::class, [
            'label'=> $this->translator->trans('Caracteristicas',[],'public'),
            'class' => TipoAplicacion::class,
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                $tipos
            ],
            'choice_label' => function(TipoAplicacion $tipo, $key, $value) use ($options){
                $traduccion= $tipo->getTraducciones()->filter(
                    function($traduccion) use ($options)
                    {
                        return $traduccion->getLocale()==$options['locale'];
                    });
                if(count($traduccion)==0)
                    return $tipo->getNombre();
                else
                    return $traduccion->first()->getNombre();
            }
        ]);



        $builder->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
            'label'=>$this->translator->trans('Continuar',[],'public'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'locale' =>'es'
        ]);
    }


}
<?php


namespace App\Form;

use App\Entity\Caracteristica;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Form\DataTransformer\TipoAplicacionToPresupuestoTipoAplicacion;
use App\Form\DataTransformer\CaracteristicaToPresupuestoCaracteristicas;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PresupuestoStep2Type extends AbstractType
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

        $tipos = $options['tipos'];
        $caracs = $this->entityManager->getRepository(Caracteristica::class)->findByTipoAplicacion($options['tipos']);


        $builder->add('caracteristicas', EntityType::class, [
            'label'=> $this->translator->trans('Caracteristicas',[],'public'),
            'class' => Caracteristica::class,
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                $caracs
            ],
            'choice_label' => function(Caracteristica $carac, $key, $value) use ($options) {
                $traduccion= $carac->getTraducciones()->filter(
                    function($traduccion) use ($options)
                    {
                        return $traduccion->getLocale()==$options['locale'];
                    });
                if(count($traduccion)>0)
                {
                    $traduccion2 = $carac->getTipoAplicacion()->getTraducciones()->filter(
                        function($traduccion) use ($options)
                        {
                            return $traduccion->getLocale()==$options['locale'];
                        });
                    if(count($traduccion2)>0) {
                        return $traduccion->first()->getNombre().' ('.$traduccion2->first()->getNombre().')';
                    }
                    else
                    {
                        return $traduccion->first()->getNombre().' ('.$carac->getTipoaplicacion()->getNombre().')';
                    }

                }
                return $carac->getNombre().' ('.$carac->getTipoaplicacion()->getNombre().')';
            }
        ]);
        $builder->add('otras',TextType::class,['required'=>false]);

        $builder->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
            'label'=>$this->translator->trans('Continuar',[],'public')
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tipos'=> [],
            'locale' => 'es',
        ]);
    }


}
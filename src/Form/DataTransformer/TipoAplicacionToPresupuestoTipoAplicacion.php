<?php


namespace App\Form\DataTransformer;

use App\Entity\TipoAplicacion;
use App\Entity\PresupuestoTipoAplicacion;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TipoAplicacionToPresupuestoTipoAplicacion implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function transform($pretipos)
    {

        if (count($pretipos)==0) {
            return ;
        }

        $tipos = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($pretipos as $pretipo)
        {
            $tipos->add($pretipo->getTipoaplicacion());
        }

        return $tipos;
    }

    public function reverseTransform($tipos)
    {
        // no issue number? It's optional, so that's ok
        if (count($tipos)==0) {
            return;
        }

        $pretipos = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($tipos as $tipo)
        {
            $presupuestotipo = new PresupuestoTipoAplicacion();
            $presupuestotipo->setTipoaplicacion($tipo);
            $pretipos->add($presupuestotipo);
        }

        return $pretipos;
    }
}
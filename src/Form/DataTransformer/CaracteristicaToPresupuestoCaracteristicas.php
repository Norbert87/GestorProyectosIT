<?php


namespace App\Form\DataTransformer;


use App\Entity\Caracteristica;
use App\Entity\PresupuestoCaracteristicas;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CaracteristicaToPresupuestoCaracteristicas implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function transform($presupuestocaracteristicas)
    {

        if (count($presupuestocaracteristicas) == 0) {
            return;
        }

        $caracteristicas = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($presupuestocaracteristicas as $precarac) {
            $caracteristicas->add($precarac->getTipoaplicacion());
        }

        return $caracteristicas;
    }


    public function reverseTransform($caracteristicas)
    {
        // no issue number? It's optional, so that's ok
        if (count($caracteristicas) == 0) {
            return;
        }

        $presucaracteristicas = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($caracteristicas as $carac) {
            $presupuestocarac = new PresupuestoCaracteristicas();
            $presupuestocarac->setCaracteristica($carac);
            $presucaracteristicas->add($presupuestocarac);
        }

        return $presucaracteristicas;
    }
}
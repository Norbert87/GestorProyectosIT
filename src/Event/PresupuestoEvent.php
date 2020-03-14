<?php


namespace App\Event;

use	Symfony\Component\EventDispatcher\Event;
use	App\Entity\Presupuesto;


class PresupuestoEvent extends Event
{

    protected	$presupuesto;
    public function	__construct(Presupuesto $presupuesto)
    {
        $this->presupuesto= $presupuesto;
    }

    public function getPresupuesto(){
        return	$this->presupuesto;
    }

}
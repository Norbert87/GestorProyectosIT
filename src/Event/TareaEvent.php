<?php


namespace App\Event;

use	Symfony\Component\EventDispatcher\Event;
use	App\Entity\Tarea;

class TareaEvent extends Event
{
    private	$tarea;
    public function	__construct(Tarea $tarea)
    {
        $this->tarea= $tarea;
    }

    public function getTarea(){
        return	$this->tarea;
    }

}
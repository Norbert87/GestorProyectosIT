<?php


namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use	Symfony\Component\Workflow\Event\GuardEvent;

class ProyectoSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'workflow.proyecto.guard.terminar' => 'onComprobarProyectoParaTerminar'
        ];
    }


    public function onComprobarProyectoParaTerminar(GuardEvent $event)
    {

        $proyecto = $event->getSubject();
        foreach ($proyecto->getTareas() as $tarea) {
            if($tarea->getEstado()!='terminada')
            {
                $event->setBlocked(true);
            }
        }

    }


}
<?php


namespace App\EventSubscriber;

use App\Event\PresupuestoEvent;
use App\Event\TareaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Services\EmailManager;
use	Symfony\Component\Workflow\Event\Event as WorkflowEvent;
use Doctrine\ORM\EntityManagerInterface;



class EmailEventSubcriber implements EventSubscriberInterface
{
    private $emailService;
    private $objectManager;

    public function __construct(EmailManager $emailService, EntityManagerInterface $objectManager )
    {
        $this->emailService = $emailService;
        $this->objectManager = $objectManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            'onPresupuestoSolicitado' => 'onPresupuestoSolicitado',
            'workflow.presupuesto.entered.aprobado' => 'onPresupuestoAprobado',
            'workflow.proyecto.entered' => 'onProyectoCambio',
            'workflow.tarea.entered.terminada' => 'onTareaTerminada',
            'workflow.tarea.entered.asignada' => 'onTareaAsignada',
            'onTareacambioasignacion'=>'onTareaCambioAsignacion'
        ];
    }

    public function onPresupuestoSolicitado(PresupuestoEvent $event)
    {
        if(!$this->emailService->enviarCorreosSolicitudPresupuestoASolicitante($event->getPresupuesto()))
        {
            throw new \Exception('Fallo al enviar email a cliente');
        }
        if(!$this->emailService->enviarCorreosSolicitudPresupuestoAComerciales($event->getPresupuesto()))
        {
            throw new \Exception('Fallo al enviar email comerciales');
        }
    }

    public function onPresupuestoAprobado(WorkflowEvent $event)
    {

        if(!$this->emailService->enviarCorreosPresupuestoAprobadosolicitante($event->getSubject()))
        {
            throw new \Exception('Fallo al enviar email a cliente');
        }

        if(!$this->emailService->enviarCorreosPresupuestoAprobadoJefeproyecto($event->getSubject()))
        {
            throw new \Exception('Fallo al enviar email comerciales');
        }

        $this->objectManager->flush();
    }

    public function onProyectoCambio(WorkflowEvent $event)
    {
        if(!$this->emailService->enviarCorreosProyectoCambioEstadoSolicitante($event->getSubject()))
        {
            throw new \Exception('Fallo al enviar email a cliente');
        }

        if(!$this->emailService->enviarCorreosProyectoCambioEstadoTecnicos($event->getSubject()))
        {
            throw new \Exception('Fallo al enviar email a técnicos');
        }

        $this->objectManager->flush();
    }

    public function onTareaTerminada(WorkflowEvent $event)
    {
        if(!$this->emailService->enviarCorreoTareaTerminadaJefeProyecto($event->getSubject()))
        {
            throw new \Exception('Fallo al enviar email a jefes de proyecto');
        }

        $this->objectManager->flush();
    }

    public function onTareaAsignada(WorkflowEvent $event)
    {
        if(!$this->emailService->enviarCorreoTareaAsignadaTecnico($event->getSubject()))
        {
            throw new \Exception('Fallo al enviar email a técnico');
        }

        $this->objectManager->flush();

    }
    public function onTareaCambioAsignacion(TareaEvent $event)
    {
        if(!$this->emailService->enviarCorreoTareaAsignadaTecnico($event->getTarea()))
        {
            throw new \Exception('Fallo al enviar email a técnico');
        }

        $this->objectManager->flush();

    }

}


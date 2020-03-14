<?php


namespace App\Services;

use App\Entity\Presupuesto;
use App\Entity\Usuario;
use App\Entity\Proyecto;
use App\Entity\Tarea;

use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


/**

 * Servicio que gestiona el envío de correo

 *

 */

class EmailManager {

    private $mailer = null;
    private $entitymanager = null;
    private $templating = null;
    private $params;
    private $translator;

    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface  $manager,Environment $templating, ParameterBagInterface $params, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->entitymanager = $manager;
        $this->templating = $templating;
        $this->params = $params;
        $this->translator = $translator;
    }


    /**
     * Envía un correo a los comerciales con la
     * información de la nueva solicitud
     *
     * Devuelve true si to_do ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreosSolicitudPresupuestoAComerciales(Presupuesto $presupuesto):int {

        $total = 0.0;
        foreach ($presupuesto->getTipoAplicacion() as $tipo)
        {
            $total+=$tipo->getPreciobase();
        }

        foreach ($presupuesto->getCaracteristica() as $caracteristica)
        {
            $total+=$caracteristica->getPreciobase();
        }

        $comerciales = $this->entitymanager->getRepository(Usuario::class)->findComerciales();

        $sent = 0;
        foreach ($comerciales as $comercial) {
            $message = (new \Swift_Message($this->translator->trans('Petición de presupuesto',[],'email',$comercial->getLocale())))
                ->setFrom('noreply@app.com')
                ->setTo($comercial->getEmail())
                ->setBody($this->templating->render('email/presupuesto_summary_sales.html.twig',['presupuesto'=>$presupuesto,'total'=> $total,'locale'=>$comercial->getLocale()]),'text/html');

            $sent+=$this->mailer->send($message);
        }

        return $sent;


    }



    /**
     * Envía un correo al solicitante del presupuesto indicando
     * que se ha recibido la solicitud
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */

    public function enviarCorreosSolicitudPresupuestoASolicitante(Presupuesto $presupuesto): int {

        $total = 0.0;
        foreach ($presupuesto->getTipoAplicacion() as $tipo)
        {
            $total+=$tipo->getPreciobase();
        }

        foreach ($presupuesto->getCaracteristica() as $caracteristica)
        {
            $total+=$caracteristica->getPreciobase();
        }
        $message = (new \Swift_Message($this->translator->trans('Petición de presupuesto',[],'email',$presupuesto->getLocale())))
            ->setFrom('noreply@app.com')
            ->setTo($presupuesto->getEmail())
            ->setBody($this->templating->render('email/presupuesto_summary_client.html.twig',['presupuesto'=>$presupuesto,'total'=> $total,'locale'=>$presupuesto->getLocale()]),'text/html');

        return $this->mailer->send($message);

    }



    /**
     * Envía un correo al solicitante del presupuesto
     * indicando que se ha aprobado el presupuesto con enlance para solicitante
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreosPresupuestoAprobadosolicitante(Presupuesto $presupuesto): int {

        $message = (new \Swift_Message($this->translator->trans('Presupuesto aprobado',[],'email',$presupuesto->getLocale())))
            ->setFrom('noreply@app.com')
            ->setTo($presupuesto->getEmail())
            ->setBody($this->templating->render('email/presupuesto_approved_customer.html.twig',['presupuesto'=>$presupuesto,'host'=> $this->params->get('host'),'locale'=>$presupuesto->getLocale()]),'text/html');

        return $this->mailer->send($message);


    }

    /**
     * Envía un correo a los jefes de proyecto
     * indicando que se ha aprobado el presupuesto con nuevo proyecto y fecha de entrega (Indicada en el proyecto)
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreosPresupuestoAprobadoJefeproyecto(Presupuesto $presupuesto) {
        $jefesproyecto = $this->entitymanager->getRepository(Usuario::class)->findJefesproyecto();
        $sent = 0;
        foreach ($jefesproyecto as $jefeproyecto)
        {
            $message = (new \Swift_Message($this->translator->trans('Presupuesto aprobado',[],'email',$jefeproyecto->getLocale())))
                ->setFrom('noreply@app.com')
                ->setTo($jefeproyecto->getEmail())
                ->setBody($this->templating->render('email/presupuesto_approved_chief.html.twig',['presupuesto'=>$presupuesto,'locale'=>$jefeproyecto->getLocale()]),'text/html');

            $sent+=$this->mailer->send($message);
        }

        return $sent;

    }


    /**
     * Envía un correo al solicitante  del presupuesto
     * indicando  el nuevo estado del proyecto
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreosProyectoCambioEstadoSolicitante(Proyecto $proyecto): int {

        $presupuesto = $proyecto->getPresupuesto();

        $message = (new \Swift_Message($this->translator->trans('Cambio de estado proyecto',[],'email',$presupuesto->getLocale())))
            ->setFrom('noreply@app.com')
            ->setTo($presupuesto->getEmail())
            ->setBody($this->templating->render('email/proyecto_change_state_customer.html.twig',['proyecto'=>$proyecto,'locale' =>$presupuesto->getLocale(),'host'=>$this->params->get('host')]),'text/html');

        return $this->mailer->send($message);

    }

    /**
     * Envía un correo al solicitante y técnicos asociados del presupuesto
     * indicando  el nuevo estado del proyecto
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreosProyectoCambioEstadoTecnicos(Proyecto $proyecto):int {

        $tecnicos = $proyecto->getTecnicos();
        $emailstecnicos = [];
        foreach ($tecnicos as  $tecnico) {
            $emailstecnicos[] = $tecnico->getEmail();
        }
        $sent = 0;
        foreach ($tecnicos as $tecnico) {
            $message = (new \Swift_Message($this->translator->trans('Cambio de estado proyecto',[],'email',$tecnico->getLocale())))
                ->setFrom('noreply@app.com')
                ->setTo($tecnico->getEmail())
                ->setBody($this->templating->render('email/proyecto_change_state_employee.html.twig',['proyecto'=>$proyecto,'locale'=> $tecnico->getLocale()]),'text/html');

            $sent+=$this->mailer->send($message);
        }
        return $sent;
    }


    /**
     * Envía un correo al técnico con nueva tarea asociada
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreoTareaAsignadaTecnico(Tarea $tarea): int {

        $message = (new \Swift_Message($this->translator->trans('Tarea asignada',[],'email',$tarea->getTecnico()->getLocale())))
            ->setFrom('noreply@app.com')
            ->setTo($tarea->getTecnico()->getEmail())
            ->setBody($this->templating->render('email/tarea_assigned_employee.html.twig',['tarea'=>$tarea,'locale'=>$tarea->getTecnico()->getLocale()]),'text/html');

        return $this->mailer->send($message);

    }

    /**
     * Envía un correo al jefe de proyecto
     * indicando que la tarea a sido terminada
     *
     * Devuelve true si todo ha ido bien o false si
     * no se ha podido enviar el correo
     */
    public function enviarCorreoTareaTerminadaJefeProyecto(Tarea $tarea): int {
        $jefesproyecto = $this->entitymanager->getRepository(Usuario::class)->findJefesproyecto();

        $sent=0;
        foreach ($jefesproyecto as $jefeproyecto)
        {
            $message = (new \Swift_Message($this->translator->trans('Tarea Terminada',[],'email',$jefeproyecto->getLocale())))
                ->setFrom('noreply@app.com')
                ->setTo($jefeproyecto->getEmail())
                ->setBody($this->templating->render('email/tarea_completed_chief.html.twig',['tarea'=>$tarea,'locale'=>$jefeproyecto->getLocale()]),'text/html');

            $sent+=$this->mailer->send($message);
        }
        return $sent;

    }





}


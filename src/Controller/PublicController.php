<?php

namespace App\Controller;

use App\Entity\Caracteristica;
use App\Entity\Presupuesto;
use App\Entity\TipoAplicacion;
use App\Event\PresupuestoEvent;
use App\Form\PresupuestoStep1Type;
use App\Form\PresupuestoStep2Type;
use App\Form\PresupuestoStep3Type;
use App\Repository\ProyectoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PublicController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function home(): Response
    {
        return $this->render('public/index.html.twig');
    }

    /**
     * @Route("presupuesto/nuevo/tiposaplicacion", name="presupuesto_step1_new", methods={"GET","POST"})
     */
    public function newStep1(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        if($session->has('tiposaplicacion'))
            $session->remove('tiposaplicacion');

        $options['locale'] = $request->getLocale();
        $form = $this->createForm(PresupuestoStep1Type::class, [],$options);
        $form->handleRequest($request);


        if($form->isSubmitted() && count($form->getData()['tipoaplicacion'])==0)
        {
            $form->addError(new FormError('Debe elegir al menos una aplicación'));
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $ids=[];
            foreach ($form->getData()['tipoaplicacion'] as $tipo)
            {
                $ids[]=$tipo->getId();
            }
            $session->set('tiposaplicacion', $ids);
            return $this->redirectToRoute('presupuesto_step2_new');
        }


        return $this->render('public/presupuesto_new.html.twig', [
//            'presupuesto' => $presupuesto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("presupuesto/nuevo/caracteristicas", name="presupuesto_step2_new", methods={"GET","POST"})
     */
    public function newStep2(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        if($session->has('caracteristicas'))
            $session->remove('caracteristicas');
        if($session->has('otras'))
            $session->remove('otras');

        $tiposeligidos = $session->get('tiposaplicacion');

        $form = $this->createForm(PresupuestoStep2Type::class,null ,['tipos'=> $tiposeligidos,'locale'=> $request->getLocale()]);
        $form->handleRequest($request);


        if($form->isSubmitted() && count($form->getData()['caracteristicas'])==0)
        {
            $form->addError(new FormError('Debe elegir al menos una caracteristica'));
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $ids=[];
            foreach ($form->getData()['caracteristicas'] as $carac)
            {
                $ids[]=$carac->getId();
            }
            $session->set('caracteristicas', $ids);
            $session->set('otras', $form->getData()['otras']);
            return $this->redirectToRoute('presupuesto_new');
        }


        return $this->render('public/presupuesto_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("presupuesto/nuevo/contacto", name="presupuesto_new", methods={"GET","POST"})
     */
    public function new(Request $request, SessionInterface $session, EventDispatcherInterface $dispatcher): Response
    {
        $presupuesto = new Presupuesto();

        $tipos = [];
        $caracteristicas = [];
        $otras = "";
        if($session->has('caracteristicas'))
            $tipos = $session->get('tiposaplicacion');
        if($session->has('caracteristicas'))
            $caracteristicas = $session->get('caracteristicas');
        if($session->has('otras'))
            $otras = $session->get('otras');

        $form = $this->createForm(PresupuestoStep3Type::class, $presupuesto);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $presupuesto->setFechaSolicitud(new \DateTime('now'));
            $presupuesto->setLocale($request->getLocale());
            if(isset($otras)&& $otras!="")
                $presupuesto->setOtras($otras);
            $tipos = $entityManager->getRepository(TipoAplicacion::class)->findById($tipos);
            foreach ($tipos as $tipo)
            {
//                $pretipo = new PresupuestoTipoAplicacion();
//                $pretipo->setPresupuesto($presupuesto);
//                $pretipo->setTipoAplicacion($tipo);
//
//                $entityManager->persist($pretipo);
                $presupuesto->addTipoAplicacion($tipo);
            }
            $caracteristicas = $entityManager->getRepository(Caracteristica::class)->findById($caracteristicas);
            foreach ($caracteristicas as $carac)
            {
//                $precarac = new PresupuestoCaracteristicas();
//                $precarac->setPresupuesto($presupuesto);
//                $precarac->setCaracteristica($carac);
//
//                $entityManager->persist($precarac);
                $presupuesto->addCaracteristica($carac);
            }

            $entityManager->persist($presupuesto);
            $entityManager->flush();

            //Lanzar evento
            $event = new PresupuestoEvent($presupuesto);
            $dispatcher->dispatch('onPresupuestoSolicitado',$event);

            return $this->redirectToRoute('presupuesto_summary_show',['id'=>$presupuesto->getId()]);
        }


        return $this->render('public/presupuesto_new.html.twig', [
            'presupuesto' => $presupuesto,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("presupuesto/resumen/{id}", name="presupuesto_summary_show", methods={"GET"})
     */
    public function summary(Request $request, Presupuesto $presupuesto, EntityManagerInterface $entityManager): Response
    {
        $preTipoAplicaciones= $presupuesto->getTipoAplicacion();
        $total = 0.0;
        foreach ($preTipoAplicaciones as $tipo)
        {
            $total+=$tipo->getPreciobase();
        }

        $preCaracteristicas = $presupuesto->getCaracteristica();
        foreach ($preCaracteristicas as $caracteristica)
        {
            $total+=$caracteristica->getPreciobase();
        }
        return $this->render('public/presupuesto_summary.html.twig', [
            'pre_tipo_aplicaciones' => $preTipoAplicaciones,
            'pre_caracteristicas' => $preCaracteristicas,
            'total' => $total,
            'locale'=> $request->getLocale(),
            'otras' => $presupuesto->getOtras()
        ]);
    }

    /**
     * @Route("/proyecto/{token}", name="public_proyecto_show", methods={"GET"})
     */
    public function show($token, ProyectoRepository $repository): Response
    {
        $proyecto = $repository->findOneByTokenPublico($token);

        $tareacompletada = 0;
        if($proyecto) {
            foreach ($proyecto->getTareas() as $tarea) {
                if ($tarea->getEstado() == 'terminada') {
                    $tareacompletada++;
                }

            }
        }
        $progreso = $tareacompletada.'/'.count($proyecto->getTareas()).' Tareas completadas.';
        return $this->render('public/proyecto_show.html.twig', [
            'proyecto' => $proyecto,
            'progreso' => $progreso
        ]);
    }
}
<?php


namespace App\Controller;

use App\Entity\Proyecto;
use App\Entity\Tarea;
use App\Entity\Usuario;
use App\Event\TareaEvent;
use App\Form\PerfilType;
use App\Form\TareaType;
use App\Form\TecnicoProyectoType;
use App\Repository\ProyectoRepository;
use App\Repository\UsuarioRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


/**
 * @Route("/jefeproyecto")
 */
class JefeproyectoController extends AbstractController
{
    /**
     * @Route("/proyectos/{estado}", name="jefeproyecto_proyectos_index", methods={"GET"})
     */
    public function index(ProyectoRepository $proyectoRepository,$estado='aprobado'): Response
    {
        $proyectos = $proyectoRepository->findByEstado($estado);
        $progreso = [];
        foreach($proyectos as $proyecto)
        {
            $tareacompletada = 0;
            foreach ( $proyecto->getTareas() as $tarea) {
                if($tarea->getEstado() == 'terminada')
                {
                    $tareacompletada++;
                }

            }
            $progreso[$proyecto->getId()]=$tareacompletada.'/'.count($proyecto->getTareas());
        }
        return $this->render('jefeproyecto/proyecto_index.html.twig', [
            'proyectos' => $proyectoRepository->findByEstado($estado),
            'estado'=> $estado,
            'progreso'=>$progreso
        ]);
    }

    /**
     * @Route("/proyecto/{id}/cambiarestado/{transicion}", name="jefeproyecto_changestate_add", methods={"GET"})
     */
    public function cambiarEstado(Proyecto $proyecto, $transicion, Registry $workflow): Response
    {
        $workflow = $workflow->get($proyecto);


        if($workflow->can($proyecto,$transicion))
        {
            try
            {
                $workflow->apply($proyecto,$transicion);
            }catch (LogicException $exception)
            {
                $this->addFlash('notice',	$exception->getMessage());
            }
        }
        else
        {
            $this->addFlash('notice',	'No ha sido posible realizar el cambio de estado');
        }

        return $this->redirectToRoute('jefe_proyecto_show',['id'=>$proyecto->getId()]);
    }

    /**
     * @Route("/proyecto/{id}/tareas", name="jefeproyecto_tareas", methods={"GET"})
     */
    public function tareasProyecto(Proyecto $proyecto): Response
    {
        return $this->render('jefeproyecto/tareas_index.html.twig', [
            'proyecto' => $proyecto
        ]);
    }

    /**
     * @Route("/proyecto/{id}/tecnicos", name="jefeproyecto_tecnicos")
     */
    public function tecnicosProyecto(Request $request,Proyecto $proyecto, ObjectManager $objectManager): Response
    {
        $array = [];
        foreach($proyecto->getTecnicos() as $tecnico)
        {
            $array[]=$tecnico->getId();
        }
        $usuariossinproyecto  = $objectManager->getRepository(Proyecto::class)->findUsuariosSinProyecto($array);
        $collection =[];
        foreach ($usuariossinproyecto as $usuario)
        {
            $collection[$usuario['email']]=$usuario['id'];
        }

        $options = ['usuarios'=>$collection];

        $form = $this->createForm(TecnicoProyectoType::class, null ,$options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $idempleado =$form->get('tecnico')->getData();
            $usuario  = $objectManager->getRepository(Usuario::class)->find($idempleado);
            $proyecto->addTecnico($usuario);
            try {
                $objectManager->flush();

                $this->addFlash('ok',	'Usuario añadido correctamente');


                //Regeneramos el listado
                foreach ($usuariossinproyecto as $usuario)
                {
                    if($usuario['id']==$idempleado)
                        unset($collection[$usuario['email']]);
                }
                $options = ['usuarios'=>$collection];
                $form = $this->createForm(TecnicoProyectoType::class, null ,$options);
            }
            catch (\Exception $e)
            {
                $this->addFlash('notice',	$e->getMessage());
            }
        }

        return $this->render('jefeproyecto/tecnicos_index.html.twig', [
            'form'=> $form->createView(),
            'proyecto' => $proyecto,
        ]);
    }

    /**
     * @Route("/proyecto/{id}/nueva/tarea", name="jefeproyecto_tarea_new", methods={"GET","POST"})
     */
    public function newTarea(Request $request, Proyecto $proyecto, Registry $workflow): Response
    {
        $tarea = new Tarea();
        $tarea->setEstado('no asignada');
        $tarea->setProyecto($proyecto);
        $options['proyecto']= $proyecto;

        $form = $this->createForm(TareaType::class, $tarea, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $workflow = $workflow->get($tarea);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($tarea);
            $entityManager->flush();

            if($workflow->can($tarea,'asignar'))
            {
                $workflow->apply($tarea,'asignar');
            }

            $this->addFlash('ok',	'Tarea creada correctamente');


            return $this->redirectToRoute('jefeproyecto_tareas',['id'=>$proyecto->getId()]);
        }

        return $this->render('jefeproyecto/new_tarea.html.twig', [
            'tarea' => $tarea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/proyecto/{id}/tarea/{idtarea}/editar", name="jefeproyecto_tarea_edit", methods={"GET","POST"})
     * @Entity("tarea", expr="repository.find(idtarea)")
     */
    public function editTarea(Request $request,Proyecto $proyecto, Tarea $tarea,EventDispatcherInterface $dispatcher): Response
    {
        $tecnicoanterior = $tarea->getTecnico();
        $options['proyecto']= $proyecto;
        $form = $this->createForm(TareaType::class, $tarea,$options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $newdata = $form->getData();

            if(!$tecnicoanterior)
            {
                //Lanzar evento de cambio de asignación
                $event = new TareaEvent($newdata);
                $dispatcher->dispatch('onTareacambioasignacion',$event);
                $this->addFlash('ok',	'Tarea actualizada correctamente');
            }
            else if($tecnicoanterior->getId()!=$newdata->getTecnico()->getId())
            {
                //Lanzar evento de cambio de asignación
                $event = new TareaEvent($newdata);
                $dispatcher->dispatch('onTareacambioasignacion',$event);
            }

            return $this->redirectToRoute('jefeproyecto_tareas',['id'=>$proyecto->getId()]);
        }

        return $this->render('jefeproyecto/edit_tarea.html.twig', [
            'tarea' => $tarea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/proyecto/{id}/ver", name="jefe_proyecto_show", methods={"GET"})
     */
    public function viewProyecto(Proyecto $proyecto): Response
    {

        $tareacompletada = 0;
        foreach ( $proyecto->getTareas() as $tarea) {
            if($tarea->getEstado() == 'terminada')
            {
                $tareacompletada++;
            }

        }
        $progreso = $tareacompletada.'/'.count($proyecto->getTareas()).' Tareas completadas.';

        return $this->render('jefeproyecto/proyecto_show.html.twig', [
            'proyecto' => $proyecto,
            'progreso'=>$progreso
        ]);
    }

    /**
     * @Route("/perfil/editar", name="jefeproyecto_profile_edit", methods={"GET","POST"})
     */
    public function perfil(Request $request,Security $security, UsuarioRepository $repository, UserPasswordEncoderInterface $encoder): Response
    {

        $form = $this->createForm(PerfilType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $usuario = $security->getUser();
            $data = $form->getData();
            if(isset($data['password']))
            {
                $hashpassword = $encoder->encodePassword($usuario,$data['password']);
                $usuario->setPassword($hashpassword);
            }
            if(isset($data['locale'])) {
                $usuario->setLocale($data['locale']);
            }


            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('ok',	'Usuario actualizado correctamente');

            return $this->redirectToRoute('jefeproyecto_proyectos_index');
        }

        return $this->render('jefeproyecto/edit_perfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
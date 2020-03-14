<?php


namespace App\Controller;

use App\Entity\Proyecto;
use App\Entity\Tarea;
use App\Form\PerfilType;
use App\Repository\ProyectoRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/tecnico")
 */
class TecnicoController extends AbstractController
{
    /**
     * @Route("/proyectos", name="tecnico_proyectos_index")
     */
    public function index(Request $request, Security $security, ProyectoRepository $repository): Response
    {
        $tecnico = $security->getUser();
        $proyectos = $repository->findProyectosByTecnico($tecnico->getId());
        $progreso = [];
        $asignada = [];
        $completadatecnico = [];
        foreach($proyectos as $proyecto)
        {
            $tareacompletada = 0;
            $tareaasignada = 0;
            $tareacompletadatecnico=0;
            foreach ( $proyecto->getTareas() as $tarea) {
                if($tarea->getEstado() == 'terminada')
                {
                    $tareacompletada++;
                }
                if($tarea->getTecnico()->getId()==$tecnico->getId())
                {
                    $tareaasignada++;
                    if($tarea->getEstado() == 'terminada')
                    {
                        $tareacompletadatecnico++;
                    }
                }

            }
            $progreso[$proyecto->getId()]=$tareacompletada.'/'.count($proyecto->getTareas());
            $asignada[$proyecto->getId()]=$tareaasignada;
            $completadatecnico[$proyecto->getId()] = $tareacompletadatecnico;
        }

        return $this->render('tecnico/proyecto_index.html.twig',['proyectos'=>$proyectos,'progreso' => $progreso,'tareaasignada'=>$asignada,'tareacompletada'=>$completadatecnico]);
    }

    /**
     * @Route("/proyecto/{id}/ver", name="tecnico_proyecto_show", methods={"GET"})
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

        return $this->render('tecnico/proyecto_show.html.twig', [
            'proyecto' => $proyecto,
            'progreso'=>$progreso
        ]);
    }

    /**
     * @Route("/proyecto/{id}/tareas", name="tecnico_tareas", methods={"GET"})
     */
    public function tareasProyecto(Proyecto $proyecto, Security $security): Response
    {
        $tecnico = $security->getUser();
        $tareas= [];
        foreach ($proyecto->getTareas() as $tarea) {
            if($tarea->getTecnico()->getId()==$tecnico->getId())
            {
                $tareas[]=$tarea;
            }
        }
        return $this->render('tecnico/tareas_index.html.twig', [
            'tareas' => $tareas
        ]);
    }

    /**
     * @Route("/proyecto/{id}/tarea/{idtarea}/ver", name="tecnico_tarea_show", methods={"GET"})
     * @Entity("tarea", expr="repository.find(idtarea)")
     */
    public function showTarea(Request $request,Proyecto $proyecto, Tarea $tarea): Response
    {
        return $this->render('tecnico/tarea_show.html.twig',['tarea'=>$tarea]);
    }


    /**
     * @Route("/proyecto/{id}/tarea/{idtarea}/terminar", name="tecnico_tarea_terminar", methods={"GET"})
     * @Entity("tarea", expr="repository.find(idtarea)")
     */
    public function terminarTarea(Request $request,Proyecto $proyecto, Tarea $tarea, Registry $workflow): Response
    {
        $workflow = $workflow->get($tarea);
        if($workflow->can($tarea,'terminar'))
        {
            $workflow->apply($tarea,'terminar');
            $this->addFlash('ok',	'Tarea marcada como terminada');

        }
        else
            {
                $this->addFlash('error',	'No es posible terminar la tarea');
            }
        return $this->redirectToRoute('tecnico_tareas',['id'=>$proyecto->getId()]);
    }

    /**
     * @Route("/perfil/editar", name="tecnico_profile_edit", methods={"GET","POST"})
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

            return $this->redirectToRoute('tecnico_proyectos_index');
        }

        return $this->render('tecnico/edit_perfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
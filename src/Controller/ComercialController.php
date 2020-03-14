<?php


namespace App\Controller;

use App\Entity\Presupuesto;
use App\Entity\Proyecto;
use App\Form\PerfilType;
use App\Form\PresupuestoAprobarType;
use App\Form\PresupuestoType;
use App\Repository\PresupuestoRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/comercial")
 */
class ComercialController extends AbstractController
{
    /**
     * @Route("/", name="index_comercial")
     */
    public function index(Request $request): Response
    {

        return $this->render('comercial/index.html.twig');
    }

    /**
     * @Route("/presupuestos", name="comercial_presupuesto_index", methods={"GET"})
     */
    public function presupuesto(PresupuestoRepository $presupuestoRepository): Response
    {
        return $this->render('comercial/presupuesto_index.html.twig', [
            'presupuestos' => $presupuestoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/presupuesto/{id}/edit", name="comercial_presupuesto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Presupuesto $presupuesto): Response
    {
        $form = $this->createForm(PresupuestoType::class, $presupuesto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('ok',	'Presupuesto actualizado correctamente');

            return $this->redirectToRoute('comercial_presupuesto_index', [
                'id' => $presupuesto->getId(),
            ]);
        }

        return $this->render('comercial/presupuesto_edit.html.twig', [
            'presupuesto' => $presupuesto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/presupuesto/{id}", name="comercial_presupuesto_show", methods={"GET"})
     */
    public function show(Presupuesto $presupuesto): Response
    {
        return $this->render('comercial/presupuesto_show.html.twig', [
            'presupuesto' => $presupuesto,
        ]);
    }

    /**
     * @Route("/presupuesto/{id}/aprobar", name="comercial_presupuesto_aprobar", methods={"GET","POST"})
     */
    public function aprobar(Request $request, Presupuesto $presupuesto,Registry $workflow): Response
    {
        $form = $this->createForm(PresupuestoAprobarType::class, $presupuesto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $presupuesto=$form->getData();
            $workflow = $workflow->get($presupuesto);

            if($workflow->can($presupuesto,'aprobar'))
            {

                $proyecto = new Proyecto();
                $proyecto->setPresupuesto($presupuesto);
                $proyecto->setTokenPublico(sha1(random_bytes( 20 )));
                $proyecto->setEstado('aprobado');
                $this->getDoctrine()->getManager()->persist($proyecto);
                $presupuesto->setProyecto($proyecto);

                $workflow->apply($presupuesto,'aprobar');

                $this->addFlash('ok',	'Presupuesto aprobado correctamente');


            }
            return $this->redirectToRoute('comercial_presupuesto_index', [
                'id' => $presupuesto->getId(),
            ]);
        }

        return $this->render('comercial/presupuesto_edit.html.twig', [
            'presupuesto' => $presupuesto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/presupuesto/{id}/rechazar", name="comercial_presupuesto_rechazar", methods={"GET","POST"})
     */
    public function rechazar(Request $request, Presupuesto $presupuesto,Registry $workflow): Response
    {

        $workflow = $workflow->get($presupuesto);

        if($workflow->can($presupuesto,'rechazar'))
        {
            $workflow->apply($presupuesto,'rechazar');
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('ok',	'Presupuesto rechazado correctamente');
        }
        return $this->redirectToRoute('comercial_presupuesto_index', [
            'id' => $presupuesto->getId(),
        ]);



    }

    /**
     * @Route("/proyecto/{id}/ver", name="comercial_proyecto_show", methods={"GET"})
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

        return $this->render('comercial/proyecto_show.html.twig', [
            'proyecto' => $proyecto,
            'progreso'=>$progreso,
        ]);
    }

    /**
     * @Route("/perfil/editar", name="comercial_profile_edit", methods={"GET","POST"})
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

            return $this->redirectToRoute('comercial_presupuesto_index');
        }

        return $this->render('comercial/edit_perfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
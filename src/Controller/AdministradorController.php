<?php


namespace App\Controller;

use App\Entity\Caracteristica;
use App\Entity\CaracteristicaTraduccion;
use App\Entity\TipoAplicacion;
use App\Entity\TipoAplicacionTraduccion;
use App\Entity\Usuario;
use App\Form\CaracteristicaType;
use App\Form\PerfilType;
use App\Form\TipoAplicacionType;
use App\Form\UsuarioType;
use App\Repository\CaracteristicaRepository;
use App\Repository\TipoAplicacionRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
* @Route("/administrador")
*/
class AdministradorController extends AbstractController
{
    /**
     * @Route("/", name="index_administrador")
     */
    public function index(Request $request): Response
    {

        return $this->render('administrador/index.html.twig');
    }

    /**
     * @Route("/usuarios/", name="admin_usuarios_index", methods={"GET"})
     */
    public function listUsuarios(UsuarioRepository $usuarioRepository): Response
    {
        return $this->render('administrador/usuarios_index.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
        ]);
    }

    /**
     * @Route("usuario/{id}", name="admin_usuario_show", methods={"GET"})
     */
    public function showUsuario(Usuario $usuario): Response
    {
        return $this->render('administrador/show_usuario.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/usuario/nuevo", name="admin_usuario_new", methods={"GET","POST"})
     */
    public function newUsuario(Request $request,UserPasswordEncoderInterface $encoder): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $data = $form->getData();
            $hashpassword = $encoder->encodePassword($usuario,$usuario->getPassword());
            $usuario->setPassword($hashpassword);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usuario);
            $entityManager->flush();

            $this->addFlash('ok',	'Usuario creado correctamente');

            return $this->redirectToRoute('admin_usuarios_index');
        }

        return $this->render('administrador/new_usuario.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/usuario/{id}/editar", name="admin_usuario_edit", methods={"GET","POST"})
     */
    public function editUsuario(Request $request, Usuario $usuario,UserPasswordEncoderInterface $encoder): Response
    {
        $options = ['editar'=>true];
        $hashpasswordanterior = $usuario->getPassword();
        $form = $this->createForm(UsuarioType::class, $usuario,$options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($usuario->getPassword() && empty(trim($usuario->getPassword()))) {
                $hashpassword = $encoder->encodePassword($usuario, $usuario->getPassword());
                $usuario->setPassword($hashpassword);
            }
            else
            {
                $usuario->setPassword($hashpasswordanterior);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('ok',	'Usuario actualizado correctamente');

            return $this->redirectToRoute('admin_usuarios_index', [
                'id' => $usuario->getId(),
            ]);
        }

        return $this->render('administrador/edit_usuario.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("usuario/{id}/desactivar", name="admin_usuario_desactive", methods={"GET"})
     */
    public function desactiveUsuario(Usuario $usuario): Response
    {
        $usuario->setActive(false);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('ok',	'Usuario desactivado correctamente');
        return $this->redirectToRoute('admin_usuarios_index');
    }

    /**
     * @Route("usuario/{id}/activar", name="admin_usuario_active", methods={"GET"})
     */
    public function activeUsuario(Usuario $usuario): Response
    {
        $usuario->setActive(true);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('ok',	'Usuario activado correctamente');
        return $this->redirectToRoute('admin_usuarios_index');
    }

    /**
     * @Route("/tiposaplicacion/", name="admin_tiposaplicacion_index", methods={"GET"})
     */
    public function listTiposAplicacion(TipoAplicacionRepository $tipoRepository): Response
    {
        return $this->render('administrador/tiposaplicacion_index.html.twig', [
            'tipo_aplicacions' => $tipoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/tipoaplicacion/nuevo", name="admin_tipoaplicacion_new", methods={"GET","POST"})
     */
    public function newTipoaplicacion(Request $request): Response
    {
        $tipo = new TipoAplicacion();
        $form = $this->createForm(TipoAplicacionType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($tipo);

            $nombre_en = $form->get('nombre_en')->getData();
            if(isset($nombre_en))
            {
                $trad_en = new TipoAplicacionTraduccion();
                $trad_en->setTipoAplicacion($tipo);
                $trad_en->setNombre($nombre_en);
                $trad_en->setLocale('en');

                $entityManager->persist($trad_en);
            }



            $entityManager->flush();

            $this->addFlash('ok',	'Tipo de aplicación creada correctamente');

            return $this->redirectToRoute('admin_tiposaplicacion_index');
        }

        return $this->render('administrador/new_tipoaplicacion.html.twig', [
            '$tipo' => $tipo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("tipoaplicacion/{id}", name="admin_tipoaplicacion_show", methods={"GET"})
     */
    public function showTipoAplicacion(TipoAplicacion $tipo): Response
    {
        return $this->render('administrador/show_tipoaplicacion.html.twig', [
            'tipo_aplicacion' => $tipo,
        ]);
    }

    /**
     * @Route("/tipoaplicacion/{id}/editar", name="admin_tipoaplicacion_edit", methods={"GET","POST"})
     */
    public function editTipoAplicacion(Request $request, TipoAplicacion $tipo): Response
    {
        $traduccion =$tipo->getTraducciones()->filter(
            function($traduccion)
            {
                return $traduccion->getLocale()=='en';
            });
        $options['nombre_en']='';
        $traduccionanterior = null;
        if(count($traduccion)>0) {
            $options['nombre_en']=$traduccion->first()->getNombre();
            $traduccionanterior=$traduccion->first();
        }

        $form = $this->createForm(TipoAplicacionType::class, $tipo,$options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nombre_en = $form->get('nombre_en')->getData();
            if(isset($nombre_en))
            {
                if($traduccionanterior)
                {
                    if($traduccionanterior->getNombre()!=$nombre_en)
                    {
                        $traduccionanterior->setNombre($nombre_en);
                    }
                }
                else {
                    $trad_en = new TipoAplicacionTraduccion();
                    $trad_en->setTipoAplicacion($tipo);
                    $trad_en->setNombre($nombre_en);
                    $trad_en->setLocale('en');
                    $this->getDoctrine()->getManager()->persist($trad_en);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('ok',	'Tipo de aplicación actualizado correctamente');

            return $this->redirectToRoute('admin_tiposaplicacion_index', [
                'id' => $tipo->getId(),
            ]);
        }

        return $this->render('administrador/edit_tipoaplicacion.html.twig', [
            'tipo' => $tipo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("tipoaplicacion/{id}/desactivar", name="admin_tipoaplicacion_desactive", methods={"GET"})
     */
    public function desactiveTipoAplicacion(TipoAplicacion $tipo): Response
    {
        $tipo->setActivo(false);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('ok',	'Tipo de aplicación desactivado correctamente');
        return $this->redirectToRoute('admin_tiposaplicacion_index');
    }

    /**
     * @Route("tipoaplicacion/{id}/activar", name="admin_tipoaplicacion_active", methods={"GET"})
     */
    public function activeTipoAplicacion(TipoAplicacion $tipo): Response
    {
        $tipo->setActivo(true);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('ok',	'Tipo de aplicación activado correctamente');
        return $this->redirectToRoute('admin_tiposaplicacion_index');
    }

    /**
     * @Route("/caracteristicas/", name="admin_carateristicas_index", methods={"GET"})
     */
    public function listCarateristicas( CaracteristicaRepository $repository): Response
    {
        return $this->render('administrador/caracteristica_index.html.twig', [
            'caracteristicas' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/caracteristica/nueva", name="admin_caracteristica_new", methods={"GET","POST"})
     */
    public function newCaracteristica(Request $request): Response
    {
        $caracteristica = new Caracteristica();
        $form = $this->createForm(CaracteristicaType::class, $caracteristica);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($caracteristica);
            $nombre_en = $form->get('nombre_en')->getData();
            if(isset($nombre_en))
            {
                $trad_en = new CaracteristicaTraduccion();
                $trad_en->setCaracteristica($caracteristica);
                $trad_en->setNombre($nombre_en);
                $trad_en->setLocale('en');

                $entityManager->persist($trad_en);
            }
            $entityManager->flush();

            $this->addFlash('ok',	'Caracteristica creada correctamente');

            return $this->redirectToRoute('admin_carateristicas_index');
        }

        return $this->render('administrador/new_caracteristica.html.twig', [
            '$caracteristica' => $caracteristica,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/caracteristica/{id}/editar", name="admin_caracteristica_edit", methods={"GET","POST"})
     */
    public function editCaracteristica(Request $request, Caracteristica $caracteristica): Response
    {
        $traduccion =$caracteristica->getTraducciones()->filter(
            function($traduccion)
            {
                return $traduccion->getLocale()=='en';
            });
        $options['nombre_en']='';
        $traduccionanterior = null;
        if(count($traduccion)>0) {
            $options['nombre_en']=$traduccion->first()->getNombre();
            $traduccionanterior=$traduccion->first();
        }

        $form = $this->createForm(CaracteristicaType::class, $caracteristica,$options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nombre_en = $form->get('nombre_en')->getData();
            $entityManager = $this->getDoctrine()->getManager();
            if(isset($nombre_en))
            {
                if($traduccionanterior)
                {
                    if($traduccionanterior->getNombre()!=$nombre_en)
                    {
                        $traduccionanterior->setNombre($nombre_en);
                    }
                }
                else {
                    $trad_en = new CaracteristicaTraduccion();
                    $trad_en->setCaracteristica($caracteristica);
                    $trad_en->setNombre($nombre_en);
                    $trad_en->setLocale('en');

                    $entityManager->persist($trad_en);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('ok',	'Caracteristica actualizada correctamente');

            return $this->redirectToRoute('admin_carateristicas_index', [
                'id' => $caracteristica->getId(),
            ]);
        }

        return $this->render('administrador/edit_caracteristica.html.twig', [
            'caracteristica' => $caracteristica,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/caracteristica/{id}", name="admin_caracteristica_show", methods={"GET"})
     */
    public function showCaracteristica(Caracteristica $caracteristica): Response
    {
        return $this->render('administrador/show_caracteristica.html.twig', [
            'caracteristica' => $caracteristica,
        ]);
    }

    /**
     * @Route("/caracteristica/{id}/desactivar", name="admin_caracteristica_desactive", methods={"GET"})
     */
    public function desactiveCaracteristica(Caracteristica $caracteristica): Response
    {
        $caracteristica->setActivo(false);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('ok',	'Caracteristica desactivada correctamente');
        return $this->redirectToRoute('admin_carateristicas_index');
    }

    /**
     * @Route("/caracteristica/{id}/activar", name="admin_caracteristica_active", methods={"GET"})
     */
    public function activeCaracteristica(Caracteristica $caracteristica): Response
    {
        $caracteristica->setActivo(true);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('ok',	'Caracteristica activada correctamente');
        return $this->redirectToRoute('admin_carateristicas_index');
    }

    /**
     * @Route("/perfil/editar", name="admin_profile_edit", methods={"GET","POST"})
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

            return $this->redirectToRoute('index_administrador');
        }

        return $this->render('administrador/edit_perfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
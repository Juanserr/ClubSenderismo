<?php

namespace App\Controller;

use App\Form\RutaType;
use App\Entity\UsuarioRuta;
use App\Entity\SocioRuta;
use App\Form\DatosInscripcionType;
use App\Form\RutaConInscripcionType;
use App\Entity\Ruta;
use App\Form\EventoType;
use App\Entity\Evento;
use App\Form\DatosType;
use App\Form\SocioType;
use App\Form\ConsultorType;
use App\Form\AdministradorType;
use App\Form\EditorType;
use App\Form\DatosMaterialType;
use App\Entity\Usuario;
use App\Entity\Consultor;
use App\Entity\Administrador;
use App\Entity\Socio;
use App\Entity\MaterialDeportivo;
use App\Entity\RutaConInscripcion;
use App\Entity\SocioMaterialdeportivo;
use App\Form\ConfirmarUsuarioType;
use App\Form\DatosRutaType;
use App\Form\LoginInscripcionType;
use App\Form\MaterialDeportivoType;
use App\Form\RegistrarUsuarioType;
use App\Form\SocioMaterialdeportivoType;
use App\Form\UserType;
use App\Form\UsuarioRutaType;
use PhpParser\Node\Stmt\Const_;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UsuarioController extends AbstractController
{

#[Route('/', name: 'app_home_page')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ultimaruta = $em->getRepository(Ruta::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimoevento = $em->getRepository(Evento::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimomaterial = $em->getRepository(MaterialDeportivo::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        return $this->render('club/index.html.twig', [
            'controller_name' => 'UsuarioController',
            'ruta' => $ultimaruta,
            'evento' => $ultimoevento,
            'material' => $ultimomaterial
        ]);
    }

    #[Route('/club/rutaBuscar', name: 'rutaBuscarUsuario')]
    public function rutaBuscar(Request $request): Response
    {
        $rutas = "";
        return $this->render('club/buscarRuta.html.twig', [
            'controller_name' => 'Esta es la página para buscar una Ruta',
            'rutas' => $rutas
        ]);
    }

    #[Route('/club/eventoBuscar', name: 'eventoBuscarUsuario')]
    public function eventoBuscar(Request $request): Response
    {
        $eventos = "";

        return $this->render('club/buscarEvento.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Evento',
            'eventos' => $eventos
        ]);
    }

    #[Route('/club/materialBuscar', name: 'materialBuscarUsuario')]
    public function materialBuscar(Request $request): Response
    {
        $material = "";

        return $this->render('club/buscarMaterial.html.twig', [
            'controller_name' => 'Esta es la página para buscar Material Deportivo',
            'material' => $material
        ]);
    }

    #[Route('/club/ayuda_publico', name: 'ayudaUsuario')]
    public function ayudaUsuario(): Response
    {
        return $this->render('club/ayuda.html.twig', [
            'controller_name' => 'Esta es la página de ayuda para el Usuario Público',
        ]);
    }

    //-----------------
    //BUSCAR RUTA
    //-----------------

    #[Route('/club/ruta/buscar', name: 'buscarRutaUsuario')]
    public function buscarRuta(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Variable temporal
        $temp = "";
        //Atributos
        $nombre = $request->request->get('nombre');
        $lugarinicio = $request->request->get('lugar_inicio');
        $lugarfin = $request->request->get('lugar_fin');
        $fecha = $request->request->get('fecha');

        //A continuación se realizará una búsqueda u otra dependiendo de los parámetros introducidos
        //por la ruta:

        //Rellena todos los parámetros
        if(!empty($nombre) && !empty($lugarinicio) && !empty($lugarfin) && !empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
                ->where('ruta.nombre LIKE :nombre')
                ->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
                ->andWhere('ruta.lugar_fin LIKE :lugar_fin')
                ->andWhere('ruta.fecha LIKE :fecha')
                ->orderBy('ruta.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar_inicio','%'.$lugarinicio.'%')
                ->setParameter('lugar_fin','%'.$lugarfin.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                ->getQuery()
                ->getResult();

        //Sin fecha
        }elseif(!empty($nombre) && !empty($lugarinicio) && !empty($lugarfin) && empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            ->where('ruta.nombre LIKE :nombre')
            ->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            ->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            //->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            ->setParameter('nombre','%'.$nombre.'%')
            ->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            ->setParameter('lugar_fin','%'.$lugarfin.'%')
            //->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();
        
    //Sin lugarfin
        }elseif(!empty($nombre) && !empty($lugarinicio) && empty($lugarfin) && !empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            ->where('ruta.nombre LIKE :nombre')
            ->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            //->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            ->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            ->setParameter('nombre','%'.$nombre.'%')
            ->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            //setParameter('lugar_fin','%'.$lugarfin.'%')
            ->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();
        
        //Sin lugarinicio
        }elseif(!empty($nombre) && empty($lugarinicio) && !empty($lugarfin) && !empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            ->where('ruta.nombre LIKE :nombre')
            //->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            ->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            ->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            ->setParameter('nombre','%'.$nombre.'%')
            //->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            ->setParameter('lugar_fin','%'.$lugarfin.'%')
            ->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();
        
        //Sin lugarinicio
        }elseif(!empty($nombre) && empty($lugarinicio) && !empty($lugarfin) && !empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            ->where('ruta.nombre LIKE :nombre')
            //->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            ->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            ->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            ->setParameter('nombre','%'.$nombre.'%')
            //->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            ->setParameter('lugar_fin','%'.$lugarfin.'%')
            ->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();
            
        //Sin nombre
        }elseif(empty($nombre) && !empty($lugarinicio) && !empty($lugarfin) && !empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            //->where('ruta.nombre LIKE :nombre')
            ->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            ->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            ->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            //->setParameter('nombre','%'.$nombre.'%')
            ->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            ->setParameter('lugar_fin','%'.$lugarfin.'%')
            ->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();
        
        //Solo nombre
        }elseif(!empty($nombre) && empty($lugarinicio) && empty($lugarfin) && empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            ->where('ruta.nombre LIKE :nombre')
            //->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            //->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            //->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            ->setParameter('nombre','%'.$nombre.'%')
            //->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            //->setParameter('lugar_fin','%'.$lugarfin.'%')
            //->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();

        //Solo lugarinicio
        }elseif(empty($nombre) && !empty($lugarinicio) && empty($lugarfin) && empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            //->where('ruta.nombre LIKE :nombre')
            ->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            //->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            //->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            //->setParameter('nombre','%'.$nombre.'%')
            ->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            //->setParameter('lugar_fin','%'.$lugarfin.'%')
            //->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();

        //Solo lugarfin
        }elseif(empty($nombre) && empty($lugarinicio) && !empty($lugarfin) && empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            //->where('ruta.nombre LIKE :nombre')
            //->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            ->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            //->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            //->setParameter('nombre','%'.$nombre.'%')
            //->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            ->setParameter('lugar_fin','%'.$lugarfin.'%')
            //->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();

        //Solo fecha
        }elseif(empty($nombre) && empty($lugarinicio) && empty($lugarfin) && !empty($fecha)){
            $temp = $em->getRepository(Ruta::class)->createQueryBuilder('ruta')
            //->where('ruta.nombre LIKE :nombre')
            //->andWhere('ruta.lugar_inicio LIKE :lugar_inicio')
            //->andWhere('ruta.lugar_fin LIKE :lugar_fin')
            ->andWhere('ruta.fecha LIKE :fecha')
            ->orderBy('ruta.nombre', 'DESC')
            //->setParameter('nombre','%'.$nombre.'%')
            //->setParameter('lugar_inicio','%'.$lugarinicio.'%')
            //->setParameter('lugar_fin','%'.$lugarfin.'%')
            ->setParameter('fecha','%'.$fecha.'%')
            ->getQuery()
            ->getResult();
            
        }else {
            //Buscar todos las rutas
            $temp = $em->getRepository(Ruta::class)->findAll();
        }

        $now = date_create("now");
        for($i = 0; $i < sizeof($temp); $i++) {
            //Eliminamos la ruta que ya ha pasado de fecha
            if($temp[$i]->getFecha()<$now){
                unset($temp[$i]);
            }
            
        }
                //$encoders = [new XmlEncoder(), new JsonEncoder()];

                //TO DO
                return $this->render('club/rutasEncontradas.html.twig', [
                    'controller_name' => 'Buscar una Ruta',
                    'rutas' => $temp
                ]);
    }


    

    //-----------------
    //SOLICITAR SER SOCIO
    //-----------------

    #[Route('/club/solicitudSocio', name: 'solicitarSocioUsuario')]
    public function solicitudSocioUsuario(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $output = new ConsoleOutput();
        $usuario = new Usuario();
        $socio = new Socio();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $usuario);
        $formS = $this->createForm(SocioType::class, $socio);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            if($em->getRepository(Usuario::class)->findOneBy(array('email' => $usuario->getEmail()))){
                $usuario = $em->getRepository(Usuario::class)->findOneBy(array('email' => $usuario->getEmail()));
                $id = $usuario->getId();
                $this->addFlash('info', 'Ese email ya ha sido usado anteriormente. Si conoce la contraseña, puede seguir con la solicitud');
                return $this->redirectToRoute('loginSocio', ['id' => $id]);
            }

            $telefono= strlen((string) $usuario->getTelefono());
            if ($telefono != '9') {
                $this->addFlash(type: 'danger', message: 'El número de teléfono debe tener una longitud de 9 dígitos.');
                return $this->redirectToRoute(route: 'solicitarSocioUsuario');
            }
            
            //Fecha de Registro
            $usuario->setFechaalta(\DateTime::createFromFormat('Y-m-d',date('Y-m-d')));
            //Codificación
            $usuario->setPassword($passwordEncoder->encodePassword($usuario,$form['password']->getData()));
            //Validez
            $usuario->setValidez('0');
            //Se guarda el usuario en la base de datos
            $usuario->setRoles(array('ROLE_SOCIO'));
            $em->persist($usuario);
            $em->flush();

            $this->addFlash('success', 'Ha enviado su solicitud correctamente');
            return $this->redirectToRoute(route: 'app_home_page');
        }

        return $this->render('club/solicitudSocio.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView(),
            'formS' => $formS->createView()
        ]);
    }

    //-----------------
    //LOGIN DE SOLICITUD SOCIO
    //-----------------   

    #[Route('/club/solicitudSocio/login/{id}', name: 'loginSocio')]
    public function solicitudLogin(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $output = new ConsoleOutput();
        $usuario = $em->getRepository(Usuario::class)->findOneBy(array('id' => $id));
        $password=$usuario->getPassword();
        //CREACIÓN FORMULARIO
        $form = $this->createForm(LoginInscripcionType::class, $usuario);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            //VERIFICAMOS QUE LA CONTRASEÑA ES CORRECTA
            if(password_verify($form['password']->getData(), $password)){
                $usuario->setPassword($password);
                $usuario->setRoles(array('ROLE_SOCIO'));
                $em->persist($usuario);
                $em->flush();
                $this->addFlash('success', 'Ha enviado su solicitud correctamente');
                return $this->redirectToRoute(route: 'app_home_page');
            }

                $this->addFlash('danger','Credenciales Incorrectos');
                return $this->redirectToRoute(route: 'solicitarSocioUsuario');
        }
        
        return $this->render('club/solicitudLogin.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }




    //-----------------
    //CONSULTAR RUTA
    //-----------------   

    #[Route('/club/ruta/consultar/{id}', name: 'consultarRutaUsuario')]
    public function consultarRuta($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE RUTA
        $ruta = $em->getRepository(Ruta::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(DatosRutaType::class, $ruta);
        if($em->getRepository(RutaConInscripcion::class)->findBy(array('ruta' => $id))){
            
            $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
            //CREACIÓN FORMULARIO RUTA INSCRIPCION
            $formI = $this->createForm(RutaConInscripcionType::class, $rutaIns);
            return $this->render('club/consultarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
                'formI' => $formI->createView(),
            ]);

        }else

            return $this->render('club/consultarRuta.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
        ]);
    }


    //-----------------
    //DATOS PARA LA INSCRIPCION
    //-----------------   

    #[Route('/club/ruta/inscripcion/datos/{id}', name: 'solicitarLogin')]
    public function solicitarDatos(Request $request, $id): Response
    {
        
        $em = $this->getDoctrine()->getManager();
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
        $ruta=$em->getRepository(Ruta::class)->findOneBy(array('id' => $id));
        
        $usuario = new Usuario();
        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarUsuario');
        }
        //CREACIÓN FORMULARIO
        $form = $this->createForm(LoginInscripcionType::class, $usuario);
        $form->handleRequest($request);
        $usuarioruta = new UsuarioRuta();
        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD
            
            $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
            if($em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()))){
                $usuario = $em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()));
                $password = $usuario->getPassword();
                $inscrito = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
                if($inscrito !== null){

                    $this->addFlash('danger','Usted ya esta inscrito a esta ruta');
                    return $this->redirectToRoute(route: 'rutaBuscarUsuario');
                }
                if(password_verify($form['password']->getData(), $password)){
                    
                    $usuarioruta->setIdUsuario($usuario);
                    $usuarioruta->setIdRuta($rutaIns);
                    $usuarioruta->setRutero(false);
                    $em->persist($usuarioruta);
                    $em->flush();
                    //reducir numero de plazas
                    $rutaIns->setPlazas(($rutaIns->getPlazas()) - '1' );
                    $em->persist($rutaIns);
                    $em->flush();
                    $this->addFlash('success','Usted se ha inscrito a esta ruta correctamente');
                    return $this->redirectToRoute('visualizarInscripcion', ['id' => $id]);

                }
            }

                $this->addFlash('danger','Credenciales Incorrectos');
                return $this->redirectToRoute(route: 'rutaBuscarUsuario');
        }
        
        return $this->render('club/datosLogin.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'ruta' => $ruta
        ]);
    }


    //-----------------
    //CONFIRMAR INSCRIPCION
    //-----------------   

    #[Route('/club/ruta/inscripcion/visualizar/{id}', name: 'visualizarInscripcion')]
    public function visualizarInscripcion(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        //BÚSQUEDA DE RUTA
        $ruta = $em->getRepository(Ruta::class)->findOneBy(array('id' => $id));
        //CREACIÓN FORMULARIO
        $form = $this->createForm(DatosRutaType::class, $ruta);
        $form->handleRequest($request);
        return $this->render('club/visualizarInscripcion.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }


    //-----------------
    //INSCRIBIRSE RUTA
    //-----------------

    #[Route('/club/ruta/inscripcion/{id}', name: 'insRutaUsuario')]
    public function inscripcionRuta(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        $usuario = new Usuario();
        $usuarioruta = new UsuarioRuta();
        $form = $this->createForm(UserType::class, $usuario);

        //SE ACTUALIZA EN LA BBDD
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){


            $usuario->setFechaalta(\DateTime::createFromFormat('Y-m-d',date('Y-m-d'))); 
            //Codificación
            $usuario->setPassword($passwordEncoder->encodePassword($usuario,$form['password']->getData()));
            //Validez a 0
            $usuario->setValidez('0');

            $em->persist($usuario);
            $em->flush();


            $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));

            $inscrito = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
            if($inscrito !== null){
                $this->addFlash('info', 'Usted ya esta inscrito a esta ruta');
                return $this->redirectToRoute(route: 'rutaBuscarUsuario');
            }

            $now = date_create("now");
            if($rutaIns->getFechaNosocio() > $now){
                $this->addFlash('info', 'Lo sentimos...Aún no se ha abierto el plazo de inscripción');
                return $this->redirectToRoute(route: 'rutaBuscarUsuario');
            }

            $usuarioruta->setIdUsuario($usuario);
            $usuarioruta->setIdRuta($rutaIns);
            $usuarioruta->setRutero(false);
            $em->persist($usuarioruta);
            $em->flush();
            //reducir numero de plazas
            $rutaIns->setPlazas(($rutaIns->getPlazas()) - '1' );
            $em->persist($rutaIns);
            $em->flush();
            $this->addFlash('success', 'Usted se ha inscrito a esta ruta correctamente');
            return $this->redirectToRoute('visualizarInscripcion', ['id' => $id]);
        //REDIRECCIÓN
        }

        return $this->render('club/formularioInscripcion.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //MIS RUTAS INSCRITAS
    //-----------------

    #[Route('/club/rutasInscritas', name: 'misRutasUsuario')]
    public function buscarMisRutas(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Conseguimos al usuario y la relacion del usuario con las rutas inscritas
        $usuario = new Usuario();
        $form = $this->createForm(LoginInscripcionType::class, $usuario);
        $form->handleRequest($request);
        
        //Conseguir todas las rutas en las que el usuario está inscrito
        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD

            if($em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()))){

                $usuario = $em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()));
                
                $password = $usuario->getPassword();

                if(password_verify($form['password']->getData(), $password)){
                    
                    $urutas = $em->getRepository(UsuarioRuta::class)->findBy(array('id_usuario' => $usuario->getId()));
                    //Conseguir todas las rutas en las que el usuario está inscrito
                    $rutas = array();
                    for($i = 0; $i < sizeof($urutas); $i++) {
                    //Cogemos la ruta con inscripcion una a una del array de 'Urutas'
                        $aux = $em->getRepository(RutaConInscripcion::class)->find(array('id' => $urutas[$i]->getIdRuta()));
                        //De las rutas con inscripcion sacamos los datos de la ruta 
                        $ruta = $em->getRepository(Ruta::class)->findOneBy(array('id' => $aux->getRuta()));
                        array_push($rutas,$ruta);
                        
                    }

                }else{

                    $this->addFlash('danger','Credenciales Incorrectos');
                    return $this->redirectToRoute(route: 'misRutasUsuario');
                }

            }else{

                $this->addFlash('danger','Credenciales Incorrectos');
                return $this->redirectToRoute(route: 'misRutasUsuario');
            }

            return $this->render('club/rutasInscritas.html.twig', [
            'controller_name' => '',
            'rutas' => $rutas
            ]);
        }
        
        return $this->render('club/comprobarLogin.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //ANULAR INSCRIPCION RUTA
    //-----------------

    #[Route('/club/rutasInscritas/anularinscripcion/{id}', name: 'anularInscripcionRutaUsuario')]
    public function anularInscripcionRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Conseguimos al usuario y la relacion del usuario con las rutas inscritas
        $usuario = new Usuario();
        $form = $this->createForm(LoginInscripcionType::class, $usuario);
        $form->handleRequest($request);
        echo '1';
        //Conseguir todas las rutas en las que el usuario está inscrito
        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD
            
            if($em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()))){

                $usuario = $em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()));
                
                $password = $usuario->getPassword();

                if(password_verify($form['password']->getData(), $password)){
                    
                    $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
                    $usuarioruta = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
                    
                    //aumentar numero de plazas
                    $rutaIns->setPlazas(($rutaIns->getPlazas()) + '1' );
                    $em->remove($usuarioruta);
                    
                    $em->persist($rutaIns);
                    $em->flush();
                    $this->addFlash('success', 'Inscripción anulada con éxito.');
                    return $this->redirectToRoute(route: 'misRutasUsuario');

                }else{

                    $this->addFlash('danger','Credenciales Incorrectos');
                    return $this->redirectToRoute(route: 'anularInscripcionRutaUsuario');
                }

            }else{

                $this->addFlash('danger','Credenciales Incorrectos');
                return $this->redirectToRoute(route: 'anularInscripcionRutaUsuario');
            }

        }
        return $this->render('club/comprobarLogin.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //BUSCAR EVENTO
    //-----------------

    #[Route('/club/evento/buscar', name: 'buscarEventoUsuario')]
    public function buscarEvento(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Variable temporal
        $temp = "";
        //Atributos
        $nombre = $request->request->get('nombre');
        $lugar = $request->request->get('lugar');
        $hora = $request->request->get('hora');
        $fecha = $request->request->get('fecha');
        $descripcion = $request->request->get('descripcion');

        //A continuación se realizará una búsqueda u otra dependiendo de los parámetros introducidos
        //por el evento:

        //Rellena todos los parámetros
        if(!empty($nombre) && !empty($lugar) && !empty($hora) && !empty($fecha) && !empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                ->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                ->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Sin descripcion
        }elseif(!empty($nombre) && !empty($lugar) && !empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();
        
        //Sin fecha
        }elseif(!empty($nombre) && !empty($lugar) && !empty($hora) && empty($fecha) && !empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                ->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                ->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();
        
        //Sin hora
        }elseif(!empty($nombre) && !empty($lugar) && empty($hora) && !empty($fecha) && !empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                ->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                ->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();
        
        //Sin lugar
        }elseif(!empty($nombre) && empty($lugar) && !empty($hora) && !empty($fecha) && !empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                ->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                ->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();
            
        //Sin nombre
        }elseif(empty($nombre) && !empty($lugar) && !empty($hora) && !empty($fecha) && !empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                ->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                ->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();
        
        //Solo nombre
        }elseif(!empty($nombre) && empty($lugar) && empty($hora) && empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo lugar
        }elseif(empty($nombre) && !empty($lugar) && empty($hora) && empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo hora
        }elseif(empty($nombre) && empty($lugar) && !empty($hora) && empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo fecha
        }elseif(empty($nombre) && empty($lugar) && empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y lugar
        }elseif(!empty($nombre) && !empty($lugar) && empty($hora) && empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y hora
        }elseif(!empty($nombre) && empty($lugar) && !empty($hora) && empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y fecha
        }elseif(!empty($nombre) && empty($lugar) && empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo lugar y hora
        }elseif(empty($nombre) && !empty($lugar) && !empty($hora) && empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                //->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                //->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo lugar y fecha
        }elseif(empty($nombre) && !empty($lugar) && empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo hora, fecha y hora
        }elseif(empty($nombre) && !empty($lugar) && !empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                //->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre, lugar, hora y fecha
        }elseif(!empty($nombre) && !empty($lugar) && !empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre, fecha y hora
        }elseif(!empty($nombre) && empty($lugar) && !empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                //->andWhere('evento.lugar LIKE :lugar')
                ->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('lugar','%'.$lugar.'%')
                ->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre, lugar y fecha
        }elseif(!empty($nombre) && !empty($lugar) && empty($hora) && !empty($fecha) && empty($descripcion)){
            $temp = $em->getRepository(Evento::class)->createQueryBuilder('evento')
                ->where('evento.nombre LIKE :nombre')
                ->andWhere('evento.lugar LIKE :lugar')
                //->andWhere('evento.hora LIKE :hora')
                ->andWhere('evento.fecha LIKE :fecha')
                //->andWhere('evento.descripcion LIKE :descripcion')
                ->orderBy('evento.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('lugar','%'.$lugar.'%')
                //->setParameter('hora','%'.$hora.'%')
                ->setParameter('fecha','%'.$fecha.'%')
                //->setParameter('descripcion','%'.$descripcion.'%')
                ->getQuery()
                ->getResult();
                
            
        }else {
            //Buscar todos los eventos
            $temp = $em->getRepository(Evento::class)->findAll();
        }

        $now = date_create("now");
        for($i = 0; $i < sizeof($temp); $i++) {
            //Eliminamos la ruta que ya ha pasado de fecha
            if($temp[$i]->getFecha()<$now){
                unset($temp[$i]);
            }
            
        }
                //$encoders = [new XmlEncoder(), new JsonEncoder()];

                //TO DO
                return $this->render('club/eventosEncontrados.html.twig', [
                    'controller_name' => '',
                    'eventos' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR EVENTO
    //-----------------   

    #[Route('/club/evento/consultar/{id}', name: 'consultarEventoUsuario')]
    public function consultarEvento($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $evento = $em->getRepository(Evento::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(EventoType::class, $evento);

        return $this->render('club/consultarEvento.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }


    //-----------------
    //BUSCAR MATERIAL DEPORTIVO
    //-----------------

    #[Route('/club/materialdeportivo/buscar', name: 'buscarMaterialUsuario')]
    public function buscarMaterial(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Variable temporal
        $temp = "";
        //Atributos
        $nombre = $request->request->get('nombre');
        $marca = $request->request->get('marca');
        $talla = $request->request->get('talla');
        $sexo = $request->request->get('sexo');
        $color = $request->request->get('color');
        $tela = $request->request->get('tela');

        //A continuación se realizará una búsqueda u otra dependiendo de los parámetros introducidos
        //por el material deportivo:

        //Rellena todos los parámetros
        if(!empty($nombre) && !empty($marca) && !empty($talla) && !empty($sexo)
            && !empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->andWhere('material.marca LIKE :marca')
                ->andWhere('material.talla LIKE :talla')
                ->andWhere('material.sexo LIKE :sexo')
                ->andWhere('material.color LIKE :color')
                ->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->setParameter('color','%'.$color.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        //Sin tela
        }elseif(!empty($nombre) && !empty($marca) && !empty($talla) && !empty($sexo)
            && !empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->andWhere('material.marca LIKE :marca')
                ->andWhere('material.talla LIKE :talla')
                ->andWhere('material.sexo LIKE :sexo')
                ->andWhere('material.color LIKE :color')
                //->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->setParameter('color','%'.$color.'%')
                //->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();
        
        //Sin color
        }elseif(!empty($nombre) && !empty($marca) && !empty($talla) && !empty($sexo)
            && empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->andWhere('material.marca LIKE :marca')
                ->andWhere('material.talla LIKE :talla')
                ->andWhere('material.sexo LIKE :sexo')
                //->andWhere('material.color LIKE :color')
                ->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                //->setParameter('color','%'.$color.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();
        
        //Sin sexo
        }elseif(!empty($nombre) && !empty($marca) && !empty($talla) && empty($sexo)
            && !empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->andWhere('material.marca LIKE :marca')
                ->andWhere('material.talla LIKE :talla')
                //->andWhere('material.sexo LIKE :sexo')
                ->andWhere('material.color LIKE :color')
                ->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                //->setParameter('sexo','%'.$sexo.'%')
                ->setParameter('color','%'.$color.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();
            
        //Sin talla
        }elseif(!empty($nombre) && !empty($marca) && empty($talla) && !empty($sexo)
            && !empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->andWhere('material.marca LIKE :marca')
                //->andWhere('material.talla LIKE :talla')
                ->andWhere('material.sexo LIKE :sexo')
                ->andWhere('material.color LIKE :color')
                ->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                //->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->setParameter('color','%'.$color.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();
        
        //Sin marca
        }elseif(!empty($nombre) && empty($marca) && !empty($talla) && !empty($sexo)
            && !empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                //->andWhere('material.marca LIKE :marca')
                ->andWhere('material.talla LIKE :talla')
                ->andWhere('material.sexo LIKE :sexo')
                ->andWhere('material.color LIKE :color')
                ->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->setParameter('color','%'.$color.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        //Sin nombre
        }elseif(empty($nombre) && !empty($marca) && !empty($talla) && !empty($sexo)
            && !empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                //->where('material.nombre LIKE :nombre')
                ->andWhere('material.marca LIKE :marca')
                ->andWhere('material.talla LIKE :talla')
                ->andWhere('material.sexo LIKE :sexo')
                ->andWhere('material.color LIKE :color')
                ->andWhere('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->setParameter('color','%'.$color.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        
        //Solo nombre
        }elseif(!empty($nombre) && empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->getQuery()
                ->getResult();

        //Solo marca
        }elseif(empty($nombre) && !empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.marca LIKE :marca')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('marca','%'.$marca.'%')
                ->getQuery()
                ->getResult();

        //Solo talla
        }elseif(empty($nombre) && empty($marca) && !empty($talla) && empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.talla LIKE :talla')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('talla','%'.$talla.'%')
                ->getQuery()
                ->getResult();

        //Solo sexo
        }elseif(empty($nombre) && empty($marca) && empty($talla) && !empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.sexo LIKE :sexo')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('sexo','%'.$sexo.'%')
                ->getQuery()
                ->getResult();

        //Solo color
        }elseif(empty($nombre) && empty($marca) && empty($talla) && empty($sexo)
            && !empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.color LIKE :color')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('color','%'.$color.'%')
                ->getQuery()
                ->getResult();

        //Solo tela
        }elseif(empty($nombre) && empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        //Solo tela
        }elseif(empty($nombre) && empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && !empty($tela) && empty($fecha)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y marca
        }elseif(!empty($nombre) && !empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->where('material.marca LIKE :marca')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('marca','%'.$marca.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y talla
        }elseif(!empty($nombre) && empty($marca) && !empty($talla) && empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->where('material.talla LIKE :talla')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y sexo
        }elseif(!empty($nombre) && empty($marca) && empty($talla) && !empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->where('material.sexo LIKE :sexo')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y color
        }elseif(!empty($nombre) && empty($marca) && empty($talla) && empty($sexo)
            && !empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->where('material.color LIKE :color')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('color','%'.$color.'%')
                ->getQuery()
                ->getResult();

        //Solo nombre y tela
        }elseif(!empty($nombre) && empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.nombre LIKE :nombre')
                ->where('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();
                
        //Solo marca y talla
        }elseif(empty($nombre) && !empty($marca) && !empty($talla) && empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.marca LIKE :marca')
                ->where('material.talla LIKE :talla')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('talla','%'.$talla.'%')
                ->getQuery()
                ->getResult(); 
                
        //Solo marca y sexo
        }elseif(empty($nombre) && !empty($marca) && empty($talla) && !empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.marca LIKE :marca')
                ->where('material.sexo LIKE :sexo')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->getQuery()
                ->getResult();

        //Solo marca y color
        }elseif(empty($nombre) && !empty($marca) && empty($talla) && empty($sexo)
            && !empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.marca LIKE :marca')
                ->where('material.color LIKE :color')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('color','%'.$color.'%')
                ->getQuery()
                ->getResult();

        //Solo marca y tela
        }elseif(empty($nombre) && !empty($marca) && empty($talla) && empty($sexo)
            && empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.marca LIKE :marca')
                ->where('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('marca','%'.$marca.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        //Solo talla y sexo
        }elseif(empty($nombre) && empty($marca) && !empty($talla) && !empty($sexo)
            && empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.talla LIKE :talla')
                ->where('material.sexo LIKE :sexo')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('sexo','%'.$sexo.'%')
                ->getQuery()
                ->getResult();

        //Solo talla y color
        }elseif(empty($nombre) && empty($marca) && !empty($talla) && empty($sexo)
            && !empty($color) && empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.talla LIKE :talla')
                ->where('material.color LIKE :color')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('color','%'.$color.'%')
                ->getQuery()
                ->getResult();

        //Solo talla y tela
        }elseif(empty($nombre) && empty($marca) && !empty($talla) && empty($sexo)
            && empty($color) && !empty($tela)){
            $temp = $em->getRepository(MaterialDeportivo::class)->createQueryBuilder('material')
                ->where('material.talla LIKE :talla')
                ->where('material.tela LIKE :tela')
                ->orderBy('material.nombre', 'DESC')
                ->setParameter('talla','%'.$talla.'%')
                ->setParameter('tela','%'.$tela.'%')
                ->getQuery()
                ->getResult();

        }else {
            //Buscar todos los materiales deportivos
            $temp = $em->getRepository(MaterialDeportivo::class)->findAll();
        }

        $now = date_create("now");
        for($i = 0; $i < sizeof($temp); $i++) {
            //Eliminamos la ruta que ya ha pasado de fecha
            if($temp[$i]->getFechaLimite()<$now){
                unset($temp[$i]);
            }
            
        }

                return $this->render('club/materialesEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'materiales' => $temp
                ]);
    }
    //-----------------
    //CONSULTAR MATERIAL
    //-----------------   

    #[Route('/club/materialdeportivo/consultar/{id}', name: 'consultarMaterialUsuario')]
    public function consultarMaterial($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(MaterialDeportivoType::class, $material);

        return $this->render('club/consultarMaterial.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }

    
}
<?php

namespace App\Controller;

namespace App\Controller;
use App\Form\RutaType;
use App\Entity\UsuarioRuta;
use App\Entity\SocioRuta;
use App\Form\DatosInscripcionType;
use App\Form\RutaConInscripcionType;
use App\Form\DatosRutaMostrarType;
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
use App\Form\MaterialDeportivoType;
use App\Form\RegistrarUsuarioType;
use App\Form\SocioMaterialdeportivoType;
use App\Form\TarjetaFederativaType;
use App\Form\UsuarioRutaType;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\AsciiSlugger;
class SocioController extends AbstractController
{
    #[Route('/socio', name: 'app_socio')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ultimaruta = $em->getRepository(Ruta::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimoevento = $em->getRepository(Evento::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimomaterial = $em->getRepository(MaterialDeportivo::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $message = "Usted se ha logueado como Socio con el correo {$usuario->getEmail()}.";
        $this->addFlash('informacion', $message);
        return $this->render('socio/index.html.twig', [
            'controller_name' => 'UsuarioController',
            'ruta' => $ultimaruta,
            'evento' => $ultimoevento,
            'material' => $ultimomaterial
        ]);
    }

    #[Route('/socio/rutaBuscar', name: 'rutaBuscarSocio')]
    public function rutaBuscar(Request $request): Response
    {
        $rutas = "";

        return $this->render('socio/buscarRuta.html.twig', [
            'controller_name' => 'Esta es la página para buscar una Ruta',
            'rutas' => $rutas
        ]);
    }

    #[Route('/socio/eventoBuscar', name: 'eventoBuscarSocio')]
    public function eventoBuscar(Request $request): Response
    {
        $eventos = "";

        return $this->render('socio/buscarEvento.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Evento',
            'eventos' => $eventos
        ]);
    }

    #[Route('/socio/materialBuscar', name: 'materialBuscarSocio')]
    public function materialBuscar(Request $request): Response
    {
        $material = "";

        return $this->render('socio/buscarMaterial.html.twig', [
            'controller_name' => 'Esta es la página para buscar Material Deportivo',
            'material' => $material
        ]);
    }

    #[Route('/socio/ayuda_socio', name: 'ayudaSocio')]
    public function ayudaSocio(): Response
    {
        return $this->render('socio/ayuda.html.twig', [
            'controller_name' => 'Esta es la página de ayuda para el Socio',
        ]);
    }

    //-----------------
    //BUSCAR RUTA
    //-----------------

    #[Route('/socio/ruta/buscar', name: 'buscarRutaSocio')]
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
                return $this->render('socio/rutasEncontradas.html.twig', [
                    'controller_name' => 'Buscar una Ruta',
                    'rutas' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR RUTA
    //-----------------   

    #[Route('/socio/ruta/consultar/{id}', name: 'consultarRutaSocio')]
    public function consultarRuta($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE RUTA
        $ruta = $em->getRepository(Ruta::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(RutaType::class, $ruta);
        if($em->getRepository(RutaConInscripcion::class)->findBy(array('ruta' => $id))){
            
            $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
            //CREACIÓN FORMULARIO RUTA INSCRIPCION
            $formI = $this->createForm(RutaConInscripcionType::class, $rutaIns);
            return $this->render('socio/consultarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
                'formI' => $formI->createView(),
            ]);

        }else

            return $this->render('socio/consultarRuta.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //INSCRIBIRSE RUTA
    //-----------------

    #[Route('/socio/ruta/inscripcion/{id}', name: 'inscripcionRutaSocio')]
    public function inscripcionRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        $usuarioruta = new UsuarioRuta();
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId()); 
        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarSocio');
        }
        $form = $this->createForm(UsuarioRutaType::class, $usuarioruta);
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));

        $now = date_create("now");
        if($rutaIns->getFechaSocio() > $now){
            $this->addFlash('info', 'Lo sentimos...Aún no se ha abierto el plazo de inscripción');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        }

        $inscrito = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
        if($inscrito !== null){
            $this->addFlash('info', 'Usted ya esta inscrito a esta ruta');
            return $this->redirectToRoute(route: 'rutaBuscarSocio');
        }
        //$this->getUser()->getId()
        //SE ACTUALIZA EN LA BBDD
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $usuarioruta->setIdUsuario($usuario);
            $usuarioruta->setIdRuta($rutaIns);
            $usuarioruta->setRutero($form["rutero"]->getData());
            $em->persist($usuarioruta);
            $em->flush();
            //reducir numero de plazas
            $rutaIns->setPlazas(($rutaIns->getPlazas()) - '1' );
            $em->persist($rutaIns);
            $em->flush();
            $this->addFlash('exito', 'Usted se ha inscrito correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarSocio');
        //REDIRECCIÓN
        }

        return $this->render('socio/inscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }


    //-----------------
    //BUSCAR MIS RUTAS INSCRITAS
    //-----------------

    #[Route('/socio/miactividad/rutas', name: 'rutasInscritasBuscarSocio')]
    public function buscarMisRutas(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Conseguimos al usuario y la relacion del usuario con las rutas inscritas
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId()); 
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

        return $this->render('socio/rutasInscritas.html.twig', [
            'controller_name' => 'Buscar una Ruta',
            'rutas' => $rutas
        ]);
}


    //-----------------
    //ANULAR INSCRIPCION RUTA
    //-----------------

    #[Route('/socio/miactividad/rutas/anularinscripcion/{id}', name: 'anularInscripcionRutaSocio')]
    public function anularInscripcionRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        //OBTENEMOS EL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId()); 
        //OBTENEMOS LA RUTA CON INSCRIPCION
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
        //OBTENEMOS LA TUPLA DE USUARIO_RUTA
        $usuarioruta = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
        $form = $this->createForm(DatosRutaMostrarType::class, $rutaIns);
        //$this->getUser()->getId()
        //SE ACTUALIZA EN LA BBDD
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->remove($usuarioruta);
            //aumentar numero de plazas
            $rutaIns->setPlazas(($rutaIns->getPlazas()) + '1' );
            $em->persist($rutaIns);
            $em->flush();
            $this->addFlash('exito', 'Inscripción anulada con éxito.');
            return $this->redirectToRoute(route: 'rutasInscritasBuscarSocio');
        //REDIRECCIÓN
        }

        return $this->render('socio/anularInscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //BUSCAR EVENTO
    //-----------------

    #[Route('/socio/evento/buscar', name: 'buscarEventoSocio')]
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
                return $this->render('socio/eventosEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'eventos' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR EVENTO
    //-----------------   

    #[Route('/socio/evento/consultar/{id}', name: 'consultarEventoSocio')]
    public function consultarEvento($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $evento = $em->getRepository(Evento::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(EventoType::class, $evento);

        return $this->render('socio/consultarEvento.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }


    //-----------------
    //BUSCAR MATERIAL DEPORTIVO
    //-----------------

    #[Route('/socio/materialdeportivo/buscar', name: 'buscarMaterialSocio')]
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

                return $this->render('socio/materialesEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'materiales' => $temp
                ]);
    }
    //-----------------
    //CONSULTAR MATERIAL
    //-----------------   

    #[Route('/socio/materialdeportivo/consultar/{id}', name: 'consultarMaterialSocio')]
    public function consultarMaterial($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(MaterialDeportivoType::class, $material);

        return $this->render('socio/consultarMaterial.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }



    //-----------------
    //SOLICITAR MATERIAL
    //-----------------

    #[Route('/socio/materialdeportivo/solicitar/{id}', name: 'solicitarMaterialSocio')]
    public function solicitarMaterial(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $socioMaterial = new SocioMaterialdeportivo();
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        $socio = $em->getRepository(Socio::class)->findOneBy(array('usuario' => $this->getUser()->getId()));
        $form = $this->createForm(DatosMaterialType::class, $material);
        $solicitado = $em->getRepository(SocioMaterialDeportivo::class)->findOneBy(array('id_usuario' => $socio->getId(), 'id_material' => $id ));
        if($solicitado !== null){
            $this->addFlash('info', 'Usted ya ha solicitado este material');
            return $this->redirectToRoute(route: 'materialBuscarSocio');
        }
        //SE ACTUALIZA EN LA BBDD
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            echo '1';
            $socioMaterial->setIdUsuario($socio);
            $socioMaterial->setIdMaterial($material);
            $socioMaterial->setEstado("En Almacén");
            $socioMaterial->setFechaSolicitud(\DateTime::createFromFormat('Y-m-d',date('Y-m-d')));
            $em->persist($socioMaterial);
            $em->flush();
            $this->addFlash('exito', 'Usted ha solicitado la prenda correctamente');
            return $this->redirectToRoute(route: 'materialBuscarSocio');
            
        }

        return $this->render('socio/solicitudPrenda.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }


    //-----------------
    //BUSCAR MIS SOLICITUDES DE MATERIAL
    //-----------------

    #[Route('/socio/miactividad/materialdeportivo', name: 'materialSolicitadoSocio')]
    public function buscarMiMaterial(Request $request): Response
    {
        //$output = new ConsoleOutput();
        $em = $this->getDoctrine()->getManager();
        //Conseguimos al usuario y la relacion del usuario con los materiales solicitados
        $socio = $em->getRepository(Socio::class)->findOneBy(array('usuario' => $this->getUser()->getId()));
        $materialsocio = $em->getRepository(SocioMaterialdeportivo::class)->findBy(array('id_usuario' => $socio->getId()));
        //Conseguir todos los materiales deportivos que el usuario ha solicitado
        $materiales = array();
        for($i = 0; $i < sizeof($materialsocio); $i++) {
            //Cogemos el material uno a uno del array de 'materialsocio'
           $material = $em->getRepository(MaterialDeportivo::class)->find(array('id' => $materialsocio[$i]->getIdMaterial()));
           array_push($materiales,$material);
        }

        return $this->render('socio/materialesSolicitados.html.twig', [
            'controller_name' => '',
            'materiales' => $materiales
        ]);
}


    //-----------------
    //CONSULTAR TU PERFIL
    //-----------------


    #[Route('/socio/perfil', name: 'consultarPerfilSocio')]
    public function consultarPerfil(): Response
    {
        $em = $this->getDoctrine()->getManager();

        //DATOS DE USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());

        $form = $this->createForm(DatosType::class, $usuario);

        return $this->render('socio/consultarPerfil.html.twig', [
            'controller_name' => 'Perfil del usuario',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //EDITAR TU PERFIL
    //-----------------


    #[Route('/socio/perfil/editar', name: 'editarPerfilSocio')]
    public function editarPerfil(Request $request): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();

        //DATOS DE USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->remove('email');
        $form->remove('roles');
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($usuario);
            $em->flush();
            $this->addFlash(type: 'success', message: 'Ha editado su perfil correctamente.');
            return $this->redirectToRoute(route: 'app_socio');
        }

        return $this->render('socio/editarPerfil.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //DAR BAJA PERFIL
    //-----------------


    #[Route('/socio/perfil/darbaja', name: 'solicitarDarBaja')]
    public function eliminarPerfil(Request $request): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();

        //DATOS USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->handleRequest($request);
        //$output->writeln($usuario->getId());
        if($form->isSubmitted() && $form->isValid()){

            $usuario->setValidez('2');
            $em->persist($usuario);
            $em->flush();
            //REDIRECCION
            $this->addFlash('success', 'La solicitud de baja se ha enviado correctamente');
            return $this->redirectToRoute(route: 'app_socio');
        }

        return $this->render('socio/eliminarPerfil.html.twig', [
            'controller_name' => 'Esta es la página para borrar el perfil. CUIDADO',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //CONSULTAR TARJETA FEDERATIVA
    //-----------------  

    #[Route('/socio/tarjetafederativa/consultar', name: 'consultarTarjetas')]
    public function consultarTarjetas(): Response
    {

        return $this->render('socio/consultarTarjetas.html.twig', [
            'controller_name' => 'Datos de tarjetas'
        ]);
    }

    //-----------------
    //SOLICITAR TARJETA FEDERATIVA
    //-----------------  

    #[Route('/socio/tarjetafederativa/solicitar/{tarjeta}', name: 'solicitarTarjeta')]
    public function solicitarTarjeta(Request $request, $tarjeta): Response
    {

        $em = $this->getDoctrine()->getManager();

        //DATOS DE USUARIO
        $socio = $em->getRepository(Socio::class)->findOneBy(array('usuario' => $this->getUser()->getId()));
        $form = $this->createForm(TarjetaFederativaType::class, $socio);
        //$output->writeln($ntarjeta);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //$output->writeln($ntarjeta);
            $socio->setTarjetaFederativa($tarjeta);
            $em->persist($socio);
            $em->flush();
            $this->addFlash('success', 'La solicitud de tarjeta federativa se ha enviado correctamente');
            return $this->redirectToRoute(route: 'app_socio');
        }

        return $this->render('socio/solicitarTarjeta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView(),
            'tarjeta' => $tarjeta
        ]);
    
    }

    //-----------------
    //CANCELAR TARJETA FEDERATIVA
    //-----------------  

    #[Route('/socio/tarjetafederativa/cancelar', name: 'cancelarTarjeta')]
    public function cancelarTarjeta(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $output = new ConsoleOutput();
        //DATOS DE USUARIO
        $socio = $em->getRepository(Socio::class)->findOneBy(array('usuario' => $this->getUser()->getId()));
        if($socio->getTarjetaFederativa() == null){
            $this->addFlash('danger', 'Usted no ha solicitado ninguna tarjeta federativa');
            return $this->redirectToRoute(route: 'app_socio');
        }

        $form = $this->createForm(TarjetaFederativaType::class, $socio);
        $ntarjeta=$socio->getTarjetaFederativa();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //$output->writeln($ntarjeta);
            $socio->setTarjetaFederativa(null);
            $em->persist($socio);
            $em->flush();
            $this->addFlash('success', 'Usted ha cancelado su tarjeta federativa.');
            return $this->redirectToRoute(route: 'app_socio');
        }
        $output->writeln($ntarjeta);
        return $this->render('socio/cancelarTarjeta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView(),
            'tarjeta' => $ntarjeta
        ]);

    }
}

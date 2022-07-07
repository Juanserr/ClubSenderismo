<?php

namespace App\Controller;
use App\Form\RutaType;
use App\Entity\UsuarioRuta;
use App\Form\DatosInscripcionType;
use App\Form\DatosRutaMostrarType;
use App\Form\EliminarRutaMostrarType;
use App\Form\RutaConInscripcionType;
use App\Entity\Ruta;
use App\Form\EventoType;
use App\Entity\Evento;
use App\Form\DatosType;
use App\Form\SocioType;
use App\Form\ConsultorType;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\AdministradorType;
use App\Form\EditorType;
use App\Entity\SocioMaterialdeportivo;
use App\Entity\Usuario;
use App\Entity\Consultor;
use App\Entity\Administrador;
use App\Entity\Socio;
use App\Entity\Editor;
use App\Entity\MaterialDeportivo;
use App\Entity\RutaConInscripcion;
use App\Form\ConfirmarUsuarioType;
use App\Form\DatosMaterialType;
use App\Form\DatosRutaType;
use App\Form\MaterialDeportivoType;
use App\Form\RegistrarUsuarioType;
use App\Form\UsuarioRutaType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;

class EditorController extends AbstractController
{
    #[Route('/editor', name: 'app_editor')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ultimaruta = $em->getRepository(Ruta::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimoevento = $em->getRepository(Evento::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimomaterial = $em->getRepository(MaterialDeportivo::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $message = "Usted se ha logueado como Editor con el correo {$usuario->getEmail()}.";
        $this->addFlash('informacion', $message);
        return $this->render('editor/index.html.twig', [
            'controller_name' => 'UsuarioController',
            'ruta' => $ultimaruta,
            'evento' => $ultimoevento,
            'material' => $ultimomaterial
        ]);
    }

    #[Route('/editor/usuarioBuscar', name: 'usuarioBuscarEditor')]
    public function usuarioBuscar(Request $request): Response
    {
        $usuarios = "";

        return $this->render('editor/buscarUsuario.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Usuario',
            'usuarios' => $usuarios
        ]);
    }

    #[Route('/editor/rutaBuscar', name: 'rutaBuscarEditor')]
    public function rutaBuscar(Request $request): Response
    {
        $rutas = "";

        return $this->render('editor/buscarRuta.html.twig', [
            'controller_name' => 'Esta es la página para buscar una Ruta',
            'rutas' => $rutas
        ]);
    }

    #[Route('/editor/eventoBuscar', name: 'eventoBuscarEditor')]
    public function eventoBuscar(Request $request): Response
    {
        $eventos = "";

        return $this->render('editor/buscarEvento.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Evento',
            'eventos' => $eventos
        ]);
    }

    #[Route('/editor/materialBuscar', name: 'materialBuscarEditor')]
    public function materialBuscar(Request $request): Response
    {
        $material = "";

        return $this->render('editor/buscarMaterial.html.twig', [
            'controller_name' => 'Esta es la página para buscar Material Deportivo',
            'material' => $material
        ]);
    }

    #[Route('/editor/ayuda_editor', name: 'ayudaEditor')]
    public function ayudaEditor(): Response
    {
        return $this->render('editor/ayuda.html.twig', [
            'controller_name' => 'Esta es la página de ayuda para el Editor',
        ]);
    }

    //-----------------
    //BUSCAR USUARIO
    //-----------------

    #[Route('/editor/usuario/buscar', name: 'buscarUsuarioEd')]
    public function buscarUsuario(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //$usuarios = "";
        //Variable temporal
        $temp = "";
        //Atributos
        $email = $request->request->get('email');
        $nombre = $request->request->get('nombre');
        $apellidos = $request->request->get('apellidos');
        $telefono = $request->request->get('telefono');
        $fecha = $request->request->get('fechaalta');

        //A continuación se realizará una búsqueda u otra dependiendo de los parámetros introducidos
        //por el usuario:

        //Rellena todos los parámetros
        if(!(empty($email)) && !empty($nombre) && !empty($apellidos) && !empty($telefono) && !empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();

        //Rellena todos los parámetros excepto la fecha de alta
        }elseif(!(empty($email)) && !empty($nombre) && !empty($apellidos) && !empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        //Rellena todos los parámetros excepto el telefono
        elseif(!(empty($email)) && !empty($nombre) && !empty($apellidos) && empty($telefono) && !empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto los apellidos
        elseif(!(empty($email)) && !empty($nombre) && empty($apellidos) && !empty($telefono) && !empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto el nombre
        elseif(!(empty($email)) && !empty($nombre) && empty($apellidos) && !empty($telefono) && !empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto el email
        elseif(!(empty($email)) && !empty($nombre) && empty($apellidos) && !empty($telefono) && !empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto la fecha y el telefono
        elseif(!(empty($email)) && !empty($nombre) && !empty($apellidos) && empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto la fecha, telefono y apellidos
        elseif(!(empty($email)) && !empty($nombre) && empty($apellidos) && empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto la fecha, telefono, apellidos y nombre
        elseif(!(empty($email)) && empty($nombre) && empty($apellidos) && empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto la fecha, apellidos
        elseif(!(empty($email)) && !empty($nombre) && empty($apellidos) && !empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto la fecha, apellidos y nombre
        elseif(!(empty($email)) && !empty($nombre) && empty($apellidos) && empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        //Rellena todos los parámetros excepto la fecha, telefono y nombre
        elseif(!(empty($email)) && empty($nombre) && !empty($apellidos) && empty($telefono) && empty($fecha)){
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        //Rellena todos los parámetros excepto la nombre y apellidos
        elseif(!(empty($email)) && empty($nombre) && empty($apellidos) && !empty($telefono) && !empty($fecha)){

            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('u')
                ->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
            //Rellena todos los parámetros excepto la nombre, apellidos y fecha
        elseif(!(empty($email)) && empty($nombre) && empty($apellidos) && !empty($telefono) && empty($fecha)){
                //Buscar Usuario por email y teléfono 
                $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                    ->where('usuario.email LIKE :email')
                    //->andWhere('usuario.nombre LIKE :nombre')
                    //->andWhere('usuario.apellidos LIKE :apellidos')
                    ->andWhere('usuario.telefono LIKE :telefono')
                    //->andWhere('usuario.fechaalta LIKE :fechaalta')
                    ->orderBy('usuario.nombre', 'DESC')
                    ->setParameter('email','%'.$email.'%')
                    //->setParameter('nombre','%'.$nombre.'%')
                    //->setParameter('apellidos','%'.$apellidos.'%')
                    ->setParameter('telefono','%'.$telefono.'%')
                    //->setParameter('fechaalta','%'.$fecha.'%')
                    ->getQuery()
                    ->getResult();
        }
        //Rellena todos los parámetros excepto la nombre, apellidos y telefono
        elseif(!(empty($email)) && empty($nombre) && empty($apellidos) && empty($telefono) && !empty($fecha)){
                    
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                ->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                ->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
    }
        
        elseif((empty($email)) && !empty($nombre) && !empty($apellidos) && !empty($telefono) && !empty($fecha)){
                    //Buscar Usuario por nombre, apellidos, teléfono y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && !empty($nombre) && !empty($apellidos) && !empty($telefono) && empty($fecha)){
            //Buscar Usuario por nombre, apellidos y teléfono
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
    }
        
        elseif((empty($email)) && !empty($nombre) && !empty($apellidos) && empty($telefono) && !empty($fecha)){
            //Buscar Usuario por nombre, apellidos y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
    }
        
        elseif((empty($email)) && !empty($nombre) && !empty($apellidos) && empty($telefono) && empty($fecha)){
            //Buscar Usuario por nombre y apellidos
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && !empty($nombre) && empty($apellidos) && !empty($telefono) && !empty($fecha)){
            //Buscar Usuario por nombre, teléfono y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && !empty($nombre) && empty($apellidos) && !empty($telefono) && empty($fecha)){
            //Buscar Usuario por nombre y teléfono
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && !empty($nombre) && empty($apellidos) && empty($telefono) && !empty($fecha)){
            //Buscar Usuario por nombre y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && !empty($nombre) && empty($apellidos) && empty($telefono) && empty($fecha)){
            //Buscar Usuario por nombre
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                ->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                ->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && !empty($apellidos) && !empty($telefono) && !empty($fecha)){
            //Buscar Usuario por apellidos, teléfono y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && !empty($apellidos) && !empty($telefono) && empty($fecha)){
            //Buscar Usuario por apellidos y teléfono
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && !empty($apellidos) && empty($telefono) && !empty($fecha)){
            //Buscar Usuario por apellidos y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //->setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && !empty($apellidos) && empty($telefono) && empty($fecha)){
            //Buscar Usuario por apellidos
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                ->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && empty($apellidos) && !empty($telefono) && !empty($fecha)){
            //Buscar Usuario por teléfono y fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && empty($apellidos) && !empty($telefono) && empty($fecha)){
            //Buscar Usuario por teléfono
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                ->andWhere('usuario.telefono LIKE :telefono')
                //->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                ->setParameter('telefono','%'.$telefono.'%')
                //->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        elseif((empty($email)) && empty($nombre) && empty($apellidos) && empty($telefono) && !empty($fecha)){
            //Buscar Usuario por fecha
            $temp = $em->getRepository(Usuario::class)->createQueryBuilder('usuario')
                //->where('usuario.email LIKE :email')
                //->andWhere('usuario.nombre LIKE :nombre')
                //->andWhere('usuario.apellidos LIKE :apellidos')
                //->andWhere('usuario.telefono LIKE :telefono')
                ->andWhere('usuario.fechaalta LIKE :fechaalta')
                ->orderBy('usuario.nombre', 'DESC')
                //>setParameter('email','%'.$email.'%')
                //->setParameter('nombre','%'.$nombre.'%')
                //->setParameter('apellidos','%'.$apellidos.'%')
                //->setParameter('telefono','%'.$telefono.'%')
                ->setParameter('fechaalta','%'.$fecha.'%')
                ->getQuery()
                ->getResult();
        }
        
        else {
            //Buscar todos los usuarios
            $temp = $em->getRepository(Usuario::class)->findAll();
        }
                //$encoders = [new XmlEncoder(), new JsonEncoder()];
                //$normalizers = [new ObjectNormalizer()];
                //$serializer = new Serializer($normalizers, $encoders);
                //$data = $serializer->serialize(array('usuarios' => $temp), JsonEncoder::FORMAT);
                //return new JsonResponse($data, Response::HTTP_OK, [], true);
                //return new Response(json_encode(array('usuarios' => $temp)));
                return $this->render('editor/usuariosEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Usuario',
                    'usuarios' => $temp
                ]);
            }

    //-----------------
    //CONSULTAR USUARIO
    //-----------------   

    #[Route('/editor/usuario/consultar/{id}', name: 'consultarUsuarioEd')]
    public function consultarUsuario($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(DatosType::class, $usuario);

        return $this->render('editor/consultarUsuario.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //EDITAR USUARIO
    //-----------------

    #[Route('/editor/usuario/editar/{id}', name: 'editarUsuarioEd')]
    public function editarUsuario(Request $request, $id): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->remove('email');
        $form->handleRequest($request);
        //¿ES ADMINISTRADOR?
        if ($usuario->esAdministrador()) {
            $this->addFlash('error','No puede editar un usuario con rol de Administrador');
            return $this->redirectToRoute(route: 'usuarioBuscarEditor');
        }

        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD

            $telefono= strlen((string) $usuario->getTelefono());
            if ($telefono != '9') {
                $this->addFlash(type: 'danger', message: 'El número de teléfono debe tener una longitud de 9 dígitos.');
                return $this->redirectToRoute(route: 'usuarioBuscarEditor');
            }
            $em->persist($usuario);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash('exito', 'El usuario se ha modificado correctamente');
            return $this->redirectToRoute(route: 'usuarioBuscarEditor');
        }

        return $this->render('editor/editarUsuario.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //VALIDAR USUARIO
    //-----------------   

    #[Route('/editor/usuario/solicitudes/{id}', name: 'validarUsuarioEd')]
    public function validarUsuario(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $socio = new Socio();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD
            $usuario->setValidez('1');
            $em->persist($usuario);
            $em->flush();

            $socio->setUsuario($usuario);
            $em->persist($socio);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash('success', 'El usuario se ha validado correctamente');
            return $this->redirectToRoute(route: 'buscarSolicitudesEditor');
        }
        return $this->render('editor/validarUsuario.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //ELIMINAR USUARIO
    //-----------------

    #[Route('/editor/usuario/eliminar/{id}', name: 'eliminarUsuarioEd')]
    public function eliminarUsuario(Request $request, $id): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();
        $output = new ConsoleOutput();
        //Buscar el usuario a eliminar
        $usuario = $em->getRepository(Usuario::class)->find($id);

        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->handleRequest($request);

        //¿ES ADMINISTRADOR?
        if ($usuario->esAdministrador()) {
            $this->addFlash('error','No puede eliminar un usuario con rol de Administrador');
            return $this->redirectToRoute(route: 'usuarioBuscarEditor');
        }

        //SI EL USUARIO ESTÁ INSCRITO A RUTAS
        $rutas = $em->getRepository(UsuarioRuta::class)->findBy(array('id_usuario' => $id));
        if($rutas !== null){
            for($i = 0; $i < sizeof($rutas); $i++) {
                //Cogemos la ruta con inscripcion una a una del array de 'Rutas'
                $rutaIns = $em->getRepository(RutaConInscripcion::class)->find(array('id' => $rutas[$i]->getIdRuta()));
                $rutaIns->setPlazas(($rutaIns->getPlazas()) + '1' );
                //BORRAMOS SU INSCRIPCION
                $em->remove($rutas[$i]);
                $em->persist($rutaIns);
                $em->flush();
            }
        }

        if($form->isSubmitted() && $form->isValid()){
            //MIRAMOS QUE TIPO DE USUARIO ES Y LO ELIMINAMOS DE SU TUPLA
            //MIRAMOS SI EL USUARIO ESTA VALIDADO O HA SOLICITADO LA BAJA
            if (($usuario->getValidez() == '1') or ($usuario->getValidez() == '2')){


                //¿ES SOCIO?
                if ($usuario->esSocio()) {
                
                    $socio = new Socio();
                    $socio = $em->getRepository(Socio::class)->findOneBy(array('usuario' => $id));
                    //SI EL USUARIO HA SOLICITADO MATERIAL
                    $output->writeln($socio->getId());
                    $materiales = $em->getRepository(SocioMaterialdeportivo::class)->findBy(array('id_usuario' => $socio->getId()));
                    
                    if($materiales != null){
                        
                        for($i = 0; $i < sizeof($materiales); $i++) {
                        //Cogemos la ruta con inscripcion una a una del array de 'Rutas'
                        //BORRAMOS SU SOLICITUD
                        $em->remove($materiales[$i]);
                        $em->flush();
                    }
                }
                    $em->remove($socio);
                    $em->flush();
                }
    
                //¿ES CONSULTOR?
                if ($usuario->esConsultor()) {
                    $consultor = new Consultor();
                    $consultor = $em->getRepository(Consultor::class)->findOneBy(array('usuario' => $id));
    
                    $em->remove($consultor);
                    $em->flush();
                }
    
                //¿ES EDITOR?
                if ($usuario->esEditor()) {
                    $editor = new Editor();
                    $editor = $em->getRepository(Editor::class)->findOneBy(array('usuario' => $id));
    
                    $em->remove($editor);
                    $em->flush();
                }
    
            }
            

            //ELIMINAMOS AL USUARIO DE LA TABLA USUARIOS TAMBIÉN
            $em->remove($usuario);
            $em->flush();
            $this->addFlash('exito','El usuario se ha eliminado correctamente');
            return $this->redirectToRoute(route: 'usuarioBuscarEditor');
        }

        return $this->render('editor/eliminarUsuario.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //REGISTRAR USUARIO
    //-----------------

    #[Route('/editor/usuario/registrar', name: 'usuarioRegistrarEditor')]
    public function registrarUsuario(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $usuario = new Usuario();
        $socio = new Socio();
        $consultor = new Consultor();
        $editor = new Editor();
        $administrador = new Administrador();
        $form = $this->createForm(RegistrarUsuarioType::class, $usuario);
        $formS = $this->createForm(SocioType::class, $socio);
        $formC = $this->createForm(ConsultorType::class, $consultor);
        $formE = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            if ($usuario->esAdministrador()) {
                $this->addFlash(type: 'danger', message: 'No puede registrar a un usuario con el rol de Administrador.');
                return $this->redirectToRoute(route: 'usuarioBuscarEditor');
            }

            $telefono= strlen((string) $usuario->getTelefono());
            if ($telefono != '9') {
                $this->addFlash(type: 'danger', message: 'El número de teléfono debe tener una longitud de 9 dígitos.');
                return $this->redirectToRoute(route: 'usuarioBuscarEditor');
            }

            //Fecha de Registro
            $usuario->setFechaalta(\DateTime::createFromFormat('Y-m-d',date('Y-m-d')));
            //Codificación
            $usuario->setPassword($passwordEncoder->encodePassword($usuario,$form['password']->getData()));
            //Se guarda el usuario en la base de datos
            $usuario->setValidez('1');
            $em->persist($usuario);
            $em->flush();
            //Se crea la tupla del tipo de usuario según el tipo elegido en el formulario
            if ($usuario->esSocio()) {
                $formS->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                $socio->setUsuario($usuario);
                $em->persist($socio);
                $em->flush();
            }

            if ($usuario->esConsultor()) {
                $formC->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                $consultor->setUsuario($usuario);
                $em->persist($consultor);
                $em->flush();
            }

            if ($usuario->esEditor()) {
                $formE->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                $editor->setUsuario($usuario);
                $em->persist($editor);
                $em->flush();
            }

            $this->addFlash(type: 'exito', message: 'El usuario se ha registrado correctamente');
            return $this->redirectToRoute(route: 'usuarioBuscarEditor');
        }

        return $this->render('editor/registrarUsuario.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView(),
            'formC' => $formC->createView(),
            'formE' => $formE->createView(),
            'formS' => $formS->createView()
        ]);
    }

    //-----------------
    //BUSCAR SOLICITUDES SOCIO
    //-----------------

    #[Route('/editor/usuario/solicitudes', name: 'buscarSolicitudesEditor')]
    public function buscarSolicitudesSocio(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $socios = $em->getRepository(Usuario::class)->findBy(array('Validez' => '0'));
        $usuarios = array();
        for($i = 0; $i < sizeof($socios); $i++) {
            if ($socios[$i]->esSocio()) {
                array_push($usuarios,$socios[$i]);
            }
            
        }
        return $this->render('editor/solicitudesSocios.html.twig', [
            'controller_name' => '',
            'usuarios' => $usuarios
        ]);
}

    //-----------------
    //BUSCAR BAJAS SOCIO
    //-----------------

    #[Route('/editor/usuario/bajas', name: 'buscarBajaEditor')]
    public function buscarBajaSocio(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $socios = $em->getRepository(Usuario::class)->findBy(array('Validez' => '2'));
        $usuarios = array();
        for($i = 0; $i < sizeof($socios); $i++) {
            if ($socios[$i]->esSocio()) {
                array_push($usuarios,$socios[$i]);
            }
            
        }
        return $this->render('editor/bajasSocios.html.twig', [
            'controller_name' => '',
            'usuarios' => $usuarios
        ]);
}



    //-----------------
    //BUSCAR RUTA
    //-----------------

    #[Route('/editor/ruta/buscar', name: 'buscarRutaEditor')]
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
                //$encoders = [new XmlEncoder(), new JsonEncoder()];

                //TO DO
                return $this->render('editor/rutasEncontradas.html.twig', [
                    'controller_name' => 'Buscar una Ruta',
                    'rutas' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR RUTA
    //-----------------   

    #[Route('/editor/ruta/consultar/{id}', name: 'consultarRutaEditor')]
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
            return $this->render('editor/consultarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
                'formI' => $formI->createView(),
            ]);

        }else

            return $this->render('editor/consultarRuta.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //EDITAR RUTA
    //-----------------

    #[Route('/editor/ruta/editar/{id}', name: 'editarRutaEditor')]
    public function editarRuta(Request $request, $id): Response
    {
        $ruta = new Ruta();
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE LA RUTA
        $ruta = $em->getRepository(Ruta::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(RutaType::class, $ruta);
        if($em->getRepository(RutaConInscripcion::class)->findBy(array('ruta' => $id))){
            $now = date_create("now");
            if($ruta->getFecha() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha de la ruta debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
            //CREACIÓN FORMULARIO RUTA INSCRIPCION
            $formI = $this->createForm(DatosInscripcionType::class, $rutaIns);
            $formI->handleRequest($request);
            if($formI->isSubmitted() && $formI->isValid()){
                //SE ACTUALIZA EN LA BBDD
                $now = date_create("now");
                if($rutaIns->getFechaSocio() < $now){
                    $this->addFlash(type: 'error', message: 'ERROR: La fecha de inscripción de socios debe ser posterior al día de hoy.');
                    return $this->redirectToRoute(route: 'rutaBuscarEditor');
                }
                if($rutaIns->getFechaNosocio() < $now){
                    $this->addFlash(type: 'error', message: 'ERROR: La fecha de inscripción de no socios de la ruta debe ser posterior al día de hoy.');
                    return $this->redirectToRoute(route: 'rutaBuscarEditor');
                }
                if($rutaIns->getFechaSocio() > $ruta->getFecha()){
                    $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de socios de la ruta debe ser anterior a la fecha de la ruta.');
                    return $this->redirectToRoute(route: 'rutaBuscarEditor');
                }
                if($rutaIns->getFechaNosocio() > $ruta->getFecha()){
                    $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de no socios de la ruta debe ser anterior a la fecha de la ruta.');
                    return $this->redirectToRoute(route: 'rutaBuscarEditor');
                }
                $em->persist($ruta);
                $em->persist($rutaIns);
                $em->flush();
                //REDIRECCIÓN
                $this->addFlash('exito','La ruta se ha editado correctamente');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            return $this->render('editor/editarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formI' => $formI->createView(),
            ]);
        }else{
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //SE ACTUALIZA EN LA BBDD
                $em->persist($ruta);
                $em->flush();
                //REDIRECCIÓN
                $this->addFlash('exito','La ruta se ha editado correctamente');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }

            return $this->render('editor/editarRuta.html.twig', [
                'controller_name' => '',
                'formulario' => $form->createView()
            ]);
        }
    }

    //-----------------
    //ELIMINAR RUTA
    //-----------------

    #[Route('/editor/ruta/eliminar/{id}', name: 'eliminarRutaEditor')]
    public function eliminarRuta(Request $request, $id): Response
    {
        $ruta = new Ruta();
        $em = $this->getDoctrine()->getManager();

        //Buscar la ruta a eliminar
        $ruta = $em->getRepository(Ruta::class)->find($id);
        //Buscar el usuario_ruta a eliminar
        $form = $this->createForm(EliminarRutaMostrarType::class, $ruta);

        //SI LA RUTA A ELIMINAR ES UNA RUTA CON INSCRIPCION

        if($em->getRepository(RutaConInscripcion::class)->findBy(array('ruta' => $id))){
            //Buscar ruta con inscripcion
            $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));

            //CREACIÓN FORMULARIO RUTA INSCRIPCION
            $formI = $this->createForm(DatosRutaMostrarType::class, $rutaIns);
            $formI->handleRequest($request);
            if($formI->isSubmitted() && $formI->isValid()){

                //BORRAMOS LOS USUARIO_RUTA CORRESPONDIENTES
                $usuariorutas = $em->getRepository(UsuarioRuta::class)->findBy(array('id_ruta' => $rutaIns->getId()));
                if($usuariorutas !== null){
                    //BORRAMOS CADA ELEMENTO DEL VECTOR

                    //$output->writeln(sizeof($usuariorutas));
                    for($i = 0; $i < sizeof($usuariorutas); $i++) {
                        $em->remove($usuariorutas[$i]);
                        
                    }
                }
               //BORRAMOS LA RUTA Y LA RUTA CON INSCRIPCION
                $em->remove($rutaIns); 
                $em->remove($ruta);
                $em->flush();
                //REDIRECCIÓN
                $this->addFlash('exito','La ruta se ha eliminado correctamente');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            return $this->render('editor/eliminarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formI' => $formI->createView(),
            ]);
        }else{
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //ELIMINAMOS LA RUTA
            $em->remove($ruta);
            $em->flush();
            $this->addFlash('exito','La ruta se ha eliminado correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }

        return $this->render('editor/eliminarRuta.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }
}

    //-----------------
    //REGISTRAR RUTA
    //-----------------

    #[Route('/editor/ruta/registrar', name: 'EditorRegistrarRuta')]
    public function registrarRuta(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $ruta = new Ruta();
        $form = $this->createForm(RutaType::class, $ruta);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $now = date_create("now");
            if($ruta->getFecha() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha de la ruta debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            if($ruta->getHoraInicio() > $ruta->getHoraFin){
                $this->addFlash(type: 'error', message: 'ERROR:  La hora de fin debe ser posterior a la de inicio.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            //Se guarda la ruta en la base de datos
            $em->persist($ruta);
            $em->flush();
            //Se crea la tupla de la ruta en el formulario
            $this->addFlash('exito','La ruta se ha registrado correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }

        return $this->render('editor/registrarRuta.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //HABILITAR INSCRIPCION RUTA
    //-----------------


    #[Route('/editor/ruta/habilitarinscripcion/{id}', name: 'habilitarInscripcionRutaEditor')]
    public function habilitarInscripcionRuta(Request $request, $id): Response
    {

        $ruta = new Ruta();
        $rutaIns = new RutaConInscripcion();
        $em = $this->getDoctrine()->getManager();
        if($em->getRepository(RutaConInscripcion::class)->findBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta ya requería inscripción.');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }
        //BÚSQUEDA DE LA RUTA
        $ruta = $em->getRepository(Ruta::class)->find($id);
        //CREACIÓN FORMULARIO
        $rutaIns->setRuta($ruta);
        $form = $this->createForm(RutaConInscripcionType::class, $rutaIns);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD
            $now = date_create("now");
            if($rutaIns->getFechaSocio() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha de inscripción de socios debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            if($rutaIns->getFechaNosocio() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha de inscripción de no socios de la ruta debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            if($rutaIns->getFechaSocio() > $ruta->getFecha()){
                $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de socios de la ruta debe ser anterior a la fecha de la ruta.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            if($rutaIns->getFechaNosocio() > $ruta->getFecha()){
                $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de no socios de la ruta debe ser anterior a la fecha de la ruta.');
                return $this->redirectToRoute(route: 'rutaBuscarEditor');
            }
            
            $em->persist($ruta);
            $em->persist($rutaIns);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash('exito','Ha habilitado la inscripción a la ruta correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }

        return $this->render('editor/registrarRutaInscripcion.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //INSCRIBIRSE RUTA
    //-----------------

    #[Route('/editor/ruta/inscripcion/{id}', name: 'inscripcionRutaEditor')]
    public function inscripcionRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        $usuarioruta = new UsuarioRuta();
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId()); 
        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }
        $form = $this->createForm(UsuarioRutaType::class, $usuarioruta);
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));

        $now = date_create("now");
        if($rutaIns->getFechaNosocio() > $now){
            $this->addFlash('info', 'Lo sentimos...Aún no se ha abierto el plazo de inscripción');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }

        $inscrito = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
        if($inscrito !== null){
            $this->addFlash('info', 'Usted ya esta inscrito a esta ruta');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
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
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        //REDIRECCIÓN
        }

        return $this->render('editor/inscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //BUSCAR MIS RUTAS INSCRITAS
    //-----------------

    #[Route('/editor/miactividad/rutas', name: 'rutasInscritasBuscarEditor')]
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

        return $this->render('editor/rutasInscritas.html.twig', [
            'controller_name' => '',
            'rutas' => $rutas
        ]);
}


//-----------------------
    //BUSCAR USUARIOS INSCRITOS
    //-----------------------

    #[Route('/editor/ruta/inscritos/{id}', name: 'usuariosInscritosEd')]
    public function inscritosRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarEditor');
        }
        //$this->getUser()->getId()
        //SE ACTUALIZA EN LA BBDD
        $usuarios = array();
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
        $inscritos = $em->getRepository(UsuarioRuta::class)->findBy(array('id_ruta' => $rutaIns->getId()));
        for($i = 0; $i < sizeof($inscritos); $i++) {
            //Cogemos el usuairo con inscripcion una a una del array de 'inscritos'
            $usuarioIns = $em->getRepository(Usuario::class)->find(array('id' => $inscritos[$i]->getIdUsuario()));
            //De las rutas con inscripcion sacamos los datos de la ruta 
            array_push($usuarios,$usuarioIns);
        }

        return $this->render('editor/usuariosInscritos.html.twig', [
            'controller_name' => 'Usuarios Inscritos',
            'usuarios' => $usuarios,
            'rutaI' => $rutaIns
        ]);
    }


    //-----------------
    //ANULAR INSCRIPCION RUTA
    //-----------------

    #[Route('/editor/miactividad/rutas/anularinscripcion/{id}', name: 'anularInscripcionRutaEditor')]
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
            return $this->redirectToRoute(route: 'rutasInscritasBuscarEditor');
        //REDIRECCIÓN
        }

        return $this->render('editor/anularInscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //BUSCAR EVENTO
    //-----------------

    #[Route('/editor/evento/buscar', name: 'buscarEventoEditor')]
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
                //$encoders = [new XmlEncoder(), new JsonEncoder()];

                //TO DO
                return $this->render('editor/eventosEncontrados.html.twig', [
                    'controller_name' => '',
                    'eventos' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR EVENTO
    //-----------------   

    #[Route('/editor/evento/consultar/{id}', name: 'consultarEventoEditor')]
    public function consultarEvento($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $evento = $em->getRepository(Evento::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(EventoType::class, $evento);

        return $this->render('editor/consultarEvento.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //EDITAR EVENTO
    //-----------------

    #[Route('/editor/evento/editar/{id}', name: 'editarEventoEditor')]
    public function editarEvento(Request $request, $id): Response
    {
        $evento = new Evento();
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL EVENTO
        $evento = $em->getRepository(Evento::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(EventoType::class, $evento);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //SE ACTUALIZA EN LA BBDD
            $em->persist($evento);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash(type: 'exito', message: 'El evento se ha editado correctamente');
            return $this->redirectToRoute(route: 'buscarEventoEditor');
        }

        return $this->render('editor/editarEvento.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //ELIMINAR EVENTO
    //-----------------

    #[Route('/editor/evento/eliminar/{id}', name: 'eliminarEventoEditor')]
    public function eliminarEvento(Request $request, $id): Response
    {
        $evento = new Evento();
        $em = $this->getDoctrine()->getManager();

        //Buscar el evento a eliminar
        $evento = $em->getRepository(Evento::class)->find($id);
        $form = $this->createForm(EventoType::class, $evento);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //ELIMINAMOS EL EVENTO
            $em->remove($evento);
            $em->flush();
            $this->addFlash(type: 'exito', message: 'El evento se ha eliminado correctamente');
            return $this->redirectToRoute(route: 'buscarEventoEditor');
        }
        
        return $this->render('editor/eliminarEvento.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //REGISTRAR EVENTO
    //-----------------

    #[Route('/editor/evento/registrar', name: 'EditorRegistrarEvento')]
    public function registrarEvento(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $evento = new Evento();
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $now = date_create("now");
            if($evento->getFecha() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha del evento debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'eventoBuscarAdmin');
            }
            //Se guarda el evento en la base de datos
            
            $em->persist($evento);
            $em->flush();
            //Se crea la tupla del evento en el formulario
            $this->addFlash(type: 'exito', message: 'El evento se ha registrado correctamente');
            return $this->redirectToRoute(route: 'buscarEventoEditor');
        }

        return $this->render('editor/registrarEvento.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }



    //-----------------
    //BUSCAR MATERIAL DEPORTIVO
    //-----------------

    #[Route('/editor/materialdeportivo/buscar', name: 'buscarMaterialEditor')]
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

                return $this->render('editor/materialesEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'materiales' => $temp
                ]);
    }
    //-----------------
    //CONSULTAR MATERIAL
    //-----------------   

    #[Route('/editor/materialdeportivo/consultar/{id}', name: 'consultarMaterialEditor')]
    public function consultarMaterial($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(MaterialDeportivoType::class, $material);

        return $this->render('editor/consultarMaterial.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }

    //-----------------
    //EDITAR MATERIAL
    //-----------------

    #[Route('/editor/materialdeportivo/editar/{id}', name: 'editarMaterialEditor')]
    public function editarMaterial(Request $request, $id): Response
    {
        $material = new MaterialDeportivo();
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL EVENTO
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(MaterialDeportivoType::class, $material);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $now = date_create("now");
            if($material->getFechaLimite() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha límite introducida debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'materialBuscarEditor');
            }
            /** @var UploadedFile $imagen */
            $imagen = $form->get('imagen_prenda')->getData();

            //La imagen es un atributo opcional(puede ser null). Si se ha subido un archivo al formulario, se procesa
            if ($imagen) {
                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);

                //Se necesita para incluir el nombre del archivo como parte de la ruta de manera segura
                $slugger = new AsciiSlugger();
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagen->guessExtension();

                //Obtener la ruta para guardar en la base de datos
                $newFilenameRoute = '\uploads\images\\'.$newFilename;

                //Mover el archivo a la carpeta donde se almacenan las imágenes
                try {
                    $imagen->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error');
                }

                //Se actualiza el atributo de la imagen para almacenar la ruta en la que se guarda la imagen, en lugar de guardar la imagen en la base de datos
                $material->setImagenPrenda($newFilenameRoute);
            }

            //SE ACTUALIZA EN LA BBDD
            $em->persist($material);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash(type: 'exito', message: 'El material se ha editado correctamente');
            return $this->redirectToRoute(route: 'materialBuscarEditor');
        }
        
        return $this->render('editor/editarMaterial.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }

    //-----------------
    //ELIMINAR MATERIAL
    //-----------------

    #[Route('/editor/materialdeportivo/eliminar/{id}', name: 'eliminarMaterialEditor')]
    public function eliminarMaterial(Request $request, $id): Response
    {
        $material = new MaterialDeportivo();
        $em = $this->getDoctrine()->getManager();

        //Buscar el material a eliminar
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        $form = $this->createForm(DatosMaterialType::class, $material);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //ELIMINAMOS EL MATERIAL
            $em->remove($material);
            $em->flush();
            $this->addFlash(type: 'exito', message: 'El material se ha eliminado correctamente');
            return $this->redirectToRoute(route: 'materialBuscarEditor');

        }

        return $this->render('editor/eliminarMaterial.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }


    //-----------------------
    //BUSCAR USUARIOS SOLICITANTES
    //-----------------------

    #[Route('/editor/materialdeportivo/solicitado/{id}', name: 'solicitudPrendaEditor')]
    public function solicitudUsuario(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $usuariofinal = new Usuario();
        $usuarioSol = new Socio();
        $material = $em->getRepository(MaterialDeportivo::class)->findOneBy(array('id' => $id));
        //$this->getUser()->getId()
        //SE ACTUALIZA EN LA BBDD
        
        $usuarios = array();
        $solicitados = $em->getRepository(SocioMaterialdeportivo::class)->findBy(array('id_material' => $id));
        
        for($i = 0; $i < sizeof($solicitados); $i++) {
            
            $usuarioSol = $em->getRepository(Socio::class)->findOneBy(array('id' => $solicitados[$i]->getIdUsuario()));
            $usuariofinal = $em->getRepository(Usuario::class)->find(array('id' => $usuarioSol->getUsuario()));
            array_push($usuarios,$usuariofinal);
        }

        return $this->render('editor/usuariosSolicitantesPrenda.html.twig', [
            'controller_name' => 'Usuarios Inscritos',
            'usuarios' => $usuarios,
            'material' => $material

        ]);
    }


    //-----------------
    //REGISTRAR MATERIAL
    //-----------------

    #[Route('/editor/materialdeportivo/registrar', name: 'EditorRegistrarMaterial')]
    public function registrarMaterial(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $material = new MaterialDeportivo();
        $form = $this->createForm(MaterialDeportivoType::class, $material);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $now = date_create("now");
            if($material->getFechaLimite() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha límite introducida debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'materialBuscarEditor');
            }

            /** @var UploadedFile $imagen */
            $imagen = $form->get('imagen_prenda')->getData();

            //La imagen es un atributo opcional(puede ser null). Si se ha subido un archivo al formulario, se procesa
            if ($imagen) {
                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);

                //Se necesita para incluir el nombre del archivo como parte de la ruta de manera segura
                $slugger = new AsciiSlugger();
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagen->guessExtension();

                //Obtener la ruta para guardar en la base de datos
                $newFilenameRoute = '\uploads\images\\'.$newFilename;

                //Mover el archivo a la carpeta donde se almacenan las imágenes
                try {
                    $imagen->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error');
                }

                //Se actualiza el atributo de la imagen para almacenar la ruta en la que se guarda la imagen, en lugar de guardar la imagen en la base de datos
                $material->setImagenPrenda($newFilenameRoute);
            }




            $em = $this->getDoctrine()->getManager();
            //Se guarda el material en la base de datos
            $em->persist($material);
            $em->flush();
            //Se crea la tupla del material en el formulario
            $this->addFlash(type: 'exito', message: 'El material se ha registrado correctamente');
            return $this->redirectToRoute(route: 'materialBuscarEditor');
        }

        return $this->render('editor/registrarMaterial.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }



    //-----------------
    //CONSULTAR TU PERFIL
    //-----------------


    #[Route('/editor/perfil', name: 'consultarPerfilEditor')]
    public function consultarPerfil(): Response
    {
        $em = $this->getDoctrine()->getManager();

        //DATOS DE USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());

        $form = $this->createForm(DatosType::class, $usuario);

        return $this->render('editor/consultarPerfil.html.twig', [
            'controller_name' => 'Perfil del usuario',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //EDITAR TU PERFIL
    //-----------------


    #[Route('/editor/perfil/editar', name: 'editarPerfilEditor')]
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
            return $this->redirectToRoute(route: 'app_editor');
        }

        return $this->render('editor/editarPerfil.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //ELIMINAR TU PERFIL
    //-----------------


    #[Route('/editor/perfil/eliminar', name: 'eliminarPerfilEditor')]
    public function eliminarPerfil(Request $request): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();

        //DATOS USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $editor = $em->getRepository(Editor::class)->findOneBy(array('usuario' => $this->getUser()->getId()));

            //CERRAMOS LA SESIÓN PARA EVITAR PROBLEMAS
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            //BORRAMOS
            $em->remove($editor);
            $em->remove($usuario);
            $em->flush();

            //Se cierra la sesión
            return $this->redirectToRoute(route: 'app_logout');
        }

        return $this->render('editor/eliminarPerfil.html.twig', [
            'controller_name' => 'Esta es la página para borrar el perfil. CUIDADO',
            'formulario' => $form->createView()
        ]);
    }



    
}


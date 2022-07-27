<?php

namespace App\Controller;

use App\Form\RutaType;
use App\Entity\UsuarioRuta;
use App\Form\DatosInscripcionType;
use App\Form\RutaConInscripcionType;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\DatosRutaMostrarType;
use App\Entity\Ruta;
use App\Form\EventoType;
use App\Entity\Evento;
use App\Form\DatosType;
use App\Form\SocioType;
use App\Form\ConsultorType;
use App\Form\AdministradorType;
use App\Form\EditorType;
use App\Entity\Usuario;
use App\Entity\Consultor;
use App\Entity\Administrador;
use App\Entity\ChangePassword;
use App\Entity\Socio;
use App\Entity\MaterialDeportivo;
use App\Entity\RutaConInscripcion;
use App\Form\ChangePasswordType;
use App\Form\ConfirmarUsuarioType;
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

class ConsultorController extends AbstractController{


    #[Route('/consultor', name: 'app_consultor')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ultimaruta = $em->getRepository(Ruta::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimoevento = $em->getRepository(Evento::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimomaterial = $em->getRepository(MaterialDeportivo::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $message = "Usted se ha identificado como Consultor con el correo {$usuario->getEmail()}";
        $this->addFlash('informacion', $message);
        return $this->render('consultor/index.html.twig', [
            'controller_name' => 'UsuarioController',
            'ruta' => $ultimaruta,
            'evento' => $ultimoevento,
            'material' => $ultimomaterial
        ]);
    }

    #[Route('/consultor/usuarioBuscar', name: 'usuarioBuscarConsultor')]
    public function usuarioBuscar(Request $request): Response
    {
        $usuarios = "";

        return $this->render('consultor/buscarUsuario.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Usuario',
            'usuarios' => $usuarios
        ]);
    }

    #[Route('/consultor/rutaBuscar', name: 'rutaBuscarConsultor')]
    public function rutaBuscar(Request $request): Response
    {
        $rutas = "";

        return $this->render('consultor/buscarRuta.html.twig', [
            'controller_name' => 'Esta es la página para buscar una Ruta',
            'rutas' => $rutas
        ]);
    }

    #[Route('/consultor/eventoBuscar', name: 'eventoBuscarConsultor')]
    public function eventoBuscar(Request $request): Response
    {
        $eventos = "";

        return $this->render('consultor/buscarEvento.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Evento',
            'eventos' => $eventos
        ]);
    }

    #[Route('/consultor/materialBuscar', name: 'materialBuscarConsultor')]
    public function materialBuscar(Request $request): Response
    {
        $material = "";

        return $this->render('consultor/buscarMaterial.html.twig', [
            'controller_name' => 'Esta es la página para buscar Material Deportivo',
            'material' => $material
        ]);
    }

    #[Route('/consultor/ayuda_consultor', name: 'ayudaConsultor')]
    public function ayudaConsultor(): Response
    {
        return $this->render('consultor/ayuda.html.twig', [
            'controller_name' => 'Esta es la página de ayuda para el Consultor',
        ]);
    }

    //-----------------
    //BUSCAR USUARIO
    //-----------------

    #[Route('/consultor/usuario/buscar', name: 'buscarUsuarioCo')]
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
                return $this->render('consultor/usuariosEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Usuario',
                    'usuarios' => $temp
                ]);
            }

    //-----------------
    //CONSULTAR USUARIO
    //-----------------   

    #[Route('/consultor/usuario/consultar/{id}', name: 'consultarUsuario')]
    public function consultarUsuario($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(DatosType::class, $usuario);

        return $this->render('consultor/consultarUsuario.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }



    //-----------------
    //BUSCAR RUTA
    //-----------------

    #[Route('/consultor/ruta/buscar', name: 'buscarRutaConsultor')]
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
                return $this->render('consultor/rutasEncontradas.html.twig', [
                    'controller_name' => 'Buscar una Ruta',
                    'rutas' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR RUTA
    //-----------------   

    #[Route('/consultor/ruta/consultar/{id}', name: 'consultarRutaConsultor')]
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
            return $this->render('consultor/consultarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
                'formI' => $formI->createView(),
            ]);

        }else

            return $this->render('consultor/consultarRuta.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //INSCRIBIRSE RUTA
    //-----------------

    #[Route('/consultor/ruta/inscripcion/{id}', name: 'inscripcionRutaConsultor')]
    public function inscripcionRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        $usuarioruta = new UsuarioRuta();
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId()); 
        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarConsultor');
        }
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
        $form = $this->createForm(DatosRutaMostrarType::class, $rutaIns);
        $now = date_create("now");
        if($rutaIns->getFechaNosocio() > $now){
            $this->addFlash('info', 'Lo sentimos...Aún no se ha abierto el plazo de inscripción');
            return $this->redirectToRoute(route: 'rutaBuscarConsultor');
        }

        $inscrito = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
        if($inscrito !== null){
            $this->addFlash('info', 'Usted ya esta inscrito a esta ruta');
            return $this->redirectToRoute(route: 'rutaBuscarConsultor');
        }
        //$this->getUser()->getId()
        //SE ACTUALIZA EN LA BBDD
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $usuarioruta->setIdUsuario($usuario);
            $usuarioruta->setIdRuta($rutaIns);
            $usuarioruta->setRutero(false);
            $em->persist($usuarioruta);
            $em->flush();
            //reducir numero de plazas
            $rutaIns->setPlazas(($rutaIns->getPlazas()) - '1' );
            $em->persist($rutaIns);
            $em->flush();
            $this->addFlash('exito', 'Usted se ha inscrito correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarConsultor');
        //REDIRECCIÓN
        }

        return $this->render('consultor/inscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //BUSCAR MIS RUTAS INSCRITAS
    //-----------------

    #[Route('/consultor/miactividad/rutas', name: 'rutasInscritasBuscarConsultor')]
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

        return $this->render('consultor/rutasInscritas.html.twig', [
            'controller_name' => '',
            'rutas' => $rutas
        ]);
}


    //-----------------
    //ANULAR INSCRIPCION RUTA
    //-----------------

    #[Route('/consultor/miactividad/rutas/anularinscripcion/{id}', name: 'anularInscripcionRutaConsultor')]
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
            return $this->redirectToRoute(route: 'rutasInscritasBuscarConsultor');
        //REDIRECCIÓN
        }

        return $this->render('consultor/anularInscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //BUSCAR EVENTO
    //-----------------

    #[Route('/consultor/evento/buscar', name: 'buscarEventoConsultor')]
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
                return $this->render('consultor/eventosEncontrados.html.twig', [
                    'controller_name' => '',
                    'eventos' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR EVENTO
    //-----------------   

    #[Route('/consultor/evento/consultar/{id}', name: 'consultarEventoConsultor')]
    public function consultarEvento($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $evento = $em->getRepository(Evento::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(EventoType::class, $evento);

        return $this->render('consultor/consultarEvento.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }


    //-----------------
    //BUSCAR MATERIAL DEPORTIVO
    //-----------------

    #[Route('/consultor/materialdeportivo/buscar', name: 'buscarMaterialConsultor')]
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

                return $this->render('consultor/materialesEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'materiales' => $temp
                ]);
    }
    //-----------------
    //CONSULTAR MATERIAL
    //-----------------   

    #[Route('/consultor/materialdeportivo/consultar/{id}', name: 'consultarMaterialConsultor')]
    public function consultarMaterial($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(MaterialDeportivoType::class, $material);

        return $this->render('consultor/consultarMaterial.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }


    //-----------------
    //CONSULTAR TU PERFIL
    //-----------------


    #[Route('/consultor/perfil', name: 'consultarPerfilConsultor')]
    public function consultarPerfil(): Response
    {
        $em = $this->getDoctrine()->getManager();

        //DATOS DE USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());

        $form = $this->createForm(DatosType::class, $usuario);

        return $this->render('consultor/consultarPerfil.html.twig', [
            'controller_name' => 'Perfil del usuario',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //EDITAR TU PERFIL
    //-----------------


    #[Route('/consultor/perfil/editar', name: 'editarPerfilConsultor')]
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
            return $this->redirectToRoute(route: 'app_consultor');
        
        }

        return $this->render('consultor/editarPerfil.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //ELIMINAR TU PERFIL
    //-----------------


    #[Route('/consultor/perfil/eliminar', name: 'eliminarPerfilConsultor')]
    public function eliminarPerfil(Request $request): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();

        //DATOS USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $consultor = $em->getRepository(Consultor::class)->findOneBy(array('usuario' => $this->getUser()->getId()));

            //CERRAMOS LA SESIÓN PARA EVITAR PROBLEMAS
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            //BORRAMOS
            $em->remove($consultor);
            $em->remove($usuario);
            $em->flush();

            //Se cierra la sesión
            return $this->redirectToRoute(route: 'app_logout');
        }

        return $this->render('consultor/eliminarPerfil.html.twig', [
            'controller_name' => 'Esta es la página para borrar el perfil. CUIDADO',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //CAMBIAR CONTRASEÑA
    //-----------------
    
    #[Route('/consultor/perfil/contraseña', name: 'cambiarContraseñaConsultor')]
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePasswordModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $user = $entityManager->find(Usuario::class, $this->getUser()->getId());
            $password = $user->getPassword();
            if(password_verify($form['oldPassword']->getData(), $password)){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash(type: 'success', message: 'Ha modificado su contraseña correctamente.');
                return $this->redirectToRoute(route: 'app_consultor');
            }

            $this->addFlash(type: 'danger', message: 'La contraseña actual introducida es incorrecta');
            return $this->redirectToRoute(route: 'app_consultor');
        }

        return $this->render('consultor/cambiarContraseña.html.twig', array(
            'changePasswordForm' => $form->createView(),
        ));        
    }
    
}

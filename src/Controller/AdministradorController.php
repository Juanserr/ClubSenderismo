<?php

namespace App\Controller;
use App\Form\RutaType;
use App\Entity\UsuarioRuta;
use App\Form\DatosInscripcionType;
use App\Form\DatosRutaMostrarType;
use App\Form\EliminarRutaMostrarType;
use App\Form\RutaConInscripcionType;
use App\Form\DatosUsuarioType;
use App\Entity\Ruta;
use App\Form\EventoType;
use App\Entity\Evento;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\DatosType;
use App\Form\SocioType;
use App\Form\ConsultorType;
use App\Form\AdministradorType;
use App\Form\EditorType;
use App\Entity\Usuario;
use App\Entity\Consultor;
use App\Entity\Administrador;
use App\Entity\ChangePassword;
use App\Form\ChangePasswordType;
use App\Entity\Socio;
use App\Entity\Editor;
use App\Entity\MaterialDeportivo;
use App\Entity\RutaConInscripcion;
use App\Entity\SocioMaterialdeportivo;
use App\Entity\SocioRuta;
use App\Form\ConfirmarUsuarioType;
use App\Form\DatosRutaType;
use App\Form\DatosMaterialType;
use App\Form\MaterialDeportivoType;
use App\Form\RegistrarUsuarioType;
use App\Form\UsuarioRutaType;
use DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Date;

class AdministradorController extends AbstractController
{
    #[Route('/administrador', name: 'app_administrador')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ultimaruta = $em->getRepository(Ruta::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimoevento = $em->getRepository(Evento::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $ultimomaterial = $em->getRepository(MaterialDeportivo::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $message = "Usted se ha identificado como Administrador con el correo {$usuario->getEmail()}";
        $this->addFlash('informacion', $message);
        return $this->render('administrador/index.html.twig', [
            'controller_name' => 'UsuarioController',
            'ruta' => $ultimaruta,
            'evento' => $ultimoevento,
            'material' => $ultimomaterial
        ]);
    }

    #[Route('/administrador/usuarioBuscar', name: 'usuarioBuscarAdmin')]
    public function usuarioBuscar(Request $request): Response
    {
        $usuarios = "";

        return $this->render('administrador/buscarUsuario.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Usuario',
            'usuarios' => $usuarios
        ]);
    }

    #[Route('/administrador/rutaBuscar', name: 'rutaBuscarAdmin')]
    public function rutaBuscar(Request $request): Response
    {
        $rutas = "";

        return $this->render('administrador/buscarRuta.html.twig', [
            'controller_name' => 'Esta es la página para buscar una Ruta',
            'rutas' => $rutas
        ]);
    }

    #[Route('/administrador/eventoBuscar', name: 'eventoBuscarAdmin')]
    public function eventoBuscar(Request $request): Response
    {
        $eventos = "";

        return $this->render('administrador/buscarEvento.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Evento',
            'eventos' => $eventos
        ]);
    }

    #[Route('/administrador/materialBuscar', name: 'materialBuscarAdmin')]
    public function materialBuscar(Request $request): Response
    {
        $material = "";

        return $this->render('administrador/buscarMaterial.html.twig', [
            'controller_name' => 'Esta es la página para buscar Material Deportivo',
            'material' => $material
        ]);
    }

    #[Route('/administrador/ayuda_administrador', name: 'ayudaAdministrador')]
    public function ayudaAdministrador(): Response
    {
        return $this->render('administrador/ayuda.html.twig', [
            'controller_name' => 'Esta es la página de ayuda para el Administrador',
        ]);
    }

    //-----------------
    //BUSCAR USUARIO
    //-----------------

    #[Route('/administrador/usuario/buscar', name: 'buscarUsuarioAd')]
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
                return $this->render('administrador/usuariosEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Usuario',
                    'usuarios' => $temp
                ]);
            }

    //-----------------
    //CONSULTAR USUARIO
    //-----------------   

    #[Route('/administrador/usuario/consultar/{id}', name: 'consultarUsuarioAd')]
    public function consultarUsuario($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DEL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(DatosType::class, $usuario);
        return $this->render('administrador/consultarUsuario.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //VALIDAR USUARIO
    //-----------------   

    #[Route('/administrador/usuario/solicitudes/{id}', name: 'validarUsuarioAd')]
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
            return $this->redirectToRoute(route: 'buscarSolicitudesAdmin');
        }
        return $this->render('administrador/validarUsuario.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //EDITAR USUARIO
    //-----------------

    #[Route('/administrador/usuario/editar/{id}', name: 'editarUsuarioAd')]
    public function editarUsuario(Request $request, $id): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();
        //BÚSQUEDA DEL USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($id);
        $roldeusuario=implode('[]', $usuario->getRoles());


        //CREACIÓN FORMULARIO
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $telefono= strlen((string) $usuario->getTelefono());
            if ($telefono != '9') {
                $this->addFlash(type: 'danger', message: 'El número de teléfono debe tener una longitud de 9 dígitos.');
                return $this->redirectToRoute(route: 'usuarioBuscarAdmin');
            }

            $rol=implode('[]', $form['roles']->getData());

            
            if($rol != $roldeusuario){
                $editor = new Editor();
                $administrador = new Administrador();
                $consultor = new Consultor();
                $socio = new Socio();
                $rutasins = $em->getRepository(UsuarioRuta::class)->findBy(array('id_usuario' => $usuario->getId()));

                if($rutasins != null){
                        
                    for($i = 0; $i < sizeof($rutasins); $i++) {
                        //Cogemos la ruta con inscripcion una a una del array de 'Rutas'
                        //BORRAMOS SU SOLICITUD
                        $em->remove($rutasins[$i]);
                        $em->flush();
                    }
                }


                if ($roldeusuario == 'ROLE_EDITOR') {

                    $editor = new Editor();
                    $editor = $em->getRepository(Editor::class)->findOneBy(array('usuario' => $id));
    
                    $em->remove($editor);
                    $em->flush();
                }
                if ($roldeusuario == 'ROLE_CONSULTOR') {

                    $consultor = new Consultor();
                    $consultor = $em->getRepository(Consultor::class)->findOneBy(array('usuario' => $id));
    
                    $em->remove($consultor);
                    $em->flush();
                }

                if ($roldeusuario == 'ROLE_ADMINISTRADOR') {

                    $administrador = new Administrador();
                    $administrador = $em->getRepository(Administrador::class)->findOneBy(array('usuario' => $id));
    
                    $em->remove($administrador);
                    $em->flush();
                }
                
                if ($roldeusuario == 'ROLE_SOCIO') {
                    $socio = $em->getRepository(Socio::class)->findOneBy(array('usuario' => $id));
                    //SI EL USUARIO HA SOLICITADO MATERIAL
                    $materiales = $em->getRepository(SocioMaterialdeportivo::class)->findBy(array('id_usuario' => $socio->getId()));
                    $rutas = $em->getRepository(SocioRuta::class)->findBy(array('id_usuario' => $socio->getId()));

                    if($rutas != null){
                        
                        for($i = 0; $i < sizeof($rutas); $i++) {
                            //Cogemos la ruta con inscripcion una a una del array de 'Rutas'
                            //BORRAMOS SU SOLICITUD
                            $em->remove($rutas[$i]);
                            $em->flush();
                        }
                    }

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
                $em->persist($usuario);
                $em->flush();
                //Se crea la tupla del tipo de usuario según el tipo elegido en el formulario
                if ($rol == 'ROLE_SOCIO') {
                    $em = $this->getDoctrine()->getManager();
                    $socio->setUsuario($usuario);
                    $em->persist($socio);
                    $em->flush();
                }

                if ($rol == 'ROLE_CONSULTOR') {
                    $em = $this->getDoctrine()->getManager();
                    $consultor->setUsuario($usuario);
                    $em->persist($consultor);
                    $em->flush();
                }

                if ($rol == 'ROLE_EDITOR') {
                    $em = $this->getDoctrine()->getManager();
                    $editor->setUsuario($usuario);
                    $em->persist($editor);
                    $em->flush();
                }

                if ($rol == 'ROLE_ADMINISTRADOR') {
                    $em = $this->getDoctrine()->getManager();
                    $administrador->setUsuario($usuario);
                    $em->persist($administrador);
                    $em->flush();
                }

                //REDIRECCIÓN
                $this->addFlash('exito','El usuario se ha editado correctamente');
                return $this->redirectToRoute(route: 'usuarioBuscarAdmin');
                    
            }
            $em->persist($usuario);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash('exito','El usuario se ha editado correctamente');
            return $this->redirectToRoute(route: 'usuarioBuscarAdmin');
                
        }
            

        return $this->render('administrador/editarUsuario.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //ELIMINAR USUARIO
    //-----------------

    #[Route('/administrador/usuario/eliminar/{id}', name: 'eliminarUsuarioAd')]
    public function eliminarUsuario(Request $request, $id): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();
        //Buscar el usuario a eliminar
        $usuario = $em->getRepository(Usuario::class)->find($id);

        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->handleRequest($request);

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
    
                //¿ES ADMINISTRADOR?
                if ($usuario->esAdministrador()) {
                    $administrador = new Administrador();
                    $administrador = $em->getRepository(Administrador::class)->findOneBy(array('usuario' => $id));
                    $em->remove($administrador);
                    $em->flush();
                }
            }
            

            //ELIMINAMOS AL USUARIO DE LA TABLA USUARIOS TAMBIÉN
            $em->remove($usuario);
            $em->flush();
            $this->addFlash('exito','El usuario se ha eliminado correctamente');
            return $this->redirectToRoute(route: 'usuarioBuscarAdmin');
        }

        return $this->render('administrador/eliminarUsuario.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //RECHAZAR USUARIO
    //-----------------

    #[Route('/administrador/usuario/solicitudes/rechazar/{id}', name: 'rechazarUsuarioAd')]
    public function rechazarUsuario(Request $request, $id): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();
        //Buscar el usuario a eliminar
        $usuario = $em->getRepository(Usuario::class)->find($id);

        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->handleRequest($request);

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
            //ELIMINAMOS AL USUARIO DE LA TABLA USUARIOS TAMBIÉN
            $usuario->setRoles(array());
            $em->persist($usuario);
            $em->flush();
            $this->addFlash('success','El usuario se ha rechazado correctamente');
            return $this->redirectToRoute(route: 'buscarSolicitudesAdmin');
        }

        return $this->render('administrador/rechazarUsuario.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //REGISTRAR USUARIO
    //-----------------

    #[Route('/administrador/usuario/registrar', name: 'registrarUsuarioAdmin')]
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
        $formA = $this->createForm(AdministradorType::class, $administrador);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
            //Fecha de Registro
            $telefono= strlen((string) $usuario->getTelefono());
            if ($telefono != '9') {
                $this->addFlash(type: 'danger', message: 'El número de teléfono debe tener una longitud de 9 dígitos.');
                return $this->redirectToRoute(route: 'usuarioBuscarAdmin');
            }
            $usuario->setFechaalta(\DateTime::createFromFormat('Y-m-d',date('Y-m-d'))); 
            //Codificación
            $usuario->setPassword($passwordEncoder->encodePassword($usuario,$form['password']->getData()));
            //Validez a 1
            $usuario->setValidez('1');
            
            //Se guarda el usuario en la base de datos

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

            if ($usuario->esAdministrador()) {
                $formA->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                $administrador->setUsuario($usuario);
                $em->persist($administrador);
                $em->flush();
            }

            $this->addFlash(type: 'exito', message: 'El usuario se ha registrado correctamente');
            return $this->redirectToRoute(route: 'usuarioBuscarAdmin');
        }
        return $this->render('administrador/registrarUsuario.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'formC' => $formC->createView(),
            'formE' => $formE->createView(),
            'formS' => $formS->createView(),
            'formA' => $formA->createView()

        ]);
    }

    //-----------------
    //BUSCAR SOLICITUDES SOCIO
    //-----------------

    #[Route('/administrador/usuario/solicitudes', name: 'buscarSolicitudesAdmin')]
    public function buscarSolicitudesSocio(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $socios = $em->getRepository(Usuario::class)->findBy(array('Validez' => '0'));
        $usuarios = array();
        for($i = 0; $i < sizeof($socios); $i++) {
            if ($socios[$i]->esSocio()) {
                array_push($usuarios,$socios[$i]);
            }
            
        }
        return $this->render('administrador/solicitudesSocios.html.twig', [
            'controller_name' => '',
            'usuarios' => $usuarios
        ]);
}

    //-----------------
    //BUSCAR BAJAS SOCIO
    //-----------------

    #[Route('/administrador/usuario/bajas', name: 'buscarBajaAdmin')]
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
        return $this->render('administrador/bajasSocios.html.twig', [
            'controller_name' => '',
            'usuarios' => $usuarios
        ]);
}



    //-----------------
    //BUSCAR RUTA
    //-----------------

    #[Route('/administrador/ruta/buscar', name: 'buscarRutaAd')]
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
                
                return $this->render('administrador/rutasEncontradas.html.twig', [
                    'controller_name' => 'Buscar una Ruta',
                    'rutas' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR RUTA
    //-----------------   

    #[Route('/administrador/ruta/consultar/{id}', name: 'consultarRutaAd')]
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
            return $this->render('administrador/consultarRutaIns.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
                'formI' => $formI->createView(),
            ]);

        }else

            return $this->render('administrador/consultarRuta.html.twig', [
                'controller_name' => 'Datos de la consulta',
                'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //EDITAR RUTA
    //-----------------

    #[Route('/administrador/ruta/editar/{id}', name: 'editarRutaAd')]
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
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
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
                    return $this->redirectToRoute(route: 'rutaBuscarAdmin');
                }
                if($rutaIns->getFechaNosocio() < $now){
                    $this->addFlash(type: 'error', message: 'ERROR: La fecha de inscripción de no socios de la ruta debe ser posterior al día de hoy.');
                    return $this->redirectToRoute(route: 'rutaBuscarAdmin');
                }
                if($rutaIns->getFechaSocio() > $ruta->getFecha()){
                    $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de socios de la ruta debe ser anterior a la fecha de la ruta.');
                    return $this->redirectToRoute(route: 'rutaBuscarAdmin');
                }
                if($rutaIns->getFechaNosocio() > $ruta->getFecha()){
                    $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de no socios de la ruta debe ser anterior a la fecha de la ruta.');
                    return $this->redirectToRoute(route: 'rutaBuscarAdmin');
                }
                $em->persist($ruta);
                $em->persist($rutaIns);
                $em->flush();
                //REDIRECCIÓN
                $this->addFlash('exito','La ruta se ha editado correctamente');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            return $this->render('administrador/editarRutaIns.html.twig', [
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
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }

            return $this->render('administrador/editarRuta.html.twig', [
                'controller_name' => '',
                'formulario' => $form->createView()
            ]);
        }
    }

    //-----------------
    //ELIMINAR RUTA
    //-----------------

    #[Route('/administrador/ruta/eliminar/{id}', name: 'eliminarRutaAd')]
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
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            return $this->render('administrador/eliminarRutaIns.html.twig', [
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
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        }

        return $this->render('administrador/eliminarRuta.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }
}

    //-----------------
    //REGISTRAR RUTA
    //-----------------

    #[Route('/administrador/ruta/registrar', name: 'AdministradorRegistrarRuta')]
    public function registrarRuta(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $ruta = new Ruta();
        $form = $this->createForm(RutaType::class, $ruta);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            //Se guarda la ruta en la base de datos
            $now = date_create("now");
            if($ruta->getFecha() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha de la ruta debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            if($ruta->getHoraInicio() > $ruta->getHoraFin()){
                $this->addFlash(type: 'error', message: 'ERROR:  La hora de fin debe ser posterior a la hora de inicio.');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            $em->persist($ruta);
            $em->flush();
            //Se crea la tupla de la ruta en el formulario
            $this->addFlash('exito','La ruta se ha registrado correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        }

        return $this->render('administrador/registrarRuta.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //HABILITAR INSCRIPCION RUTA
    //-----------------


    #[Route('/administrador/ruta/habilitarinscripcion/{id}', name: 'habilitarInscripcionRutaAd')]
    public function habilitarInscripcionRuta(Request $request, $id): Response
    {

        $ruta = new Ruta();
        $rutaIns = new RutaConInscripcion();
        $em = $this->getDoctrine()->getManager();
        if($em->getRepository(RutaConInscripcion::class)->findBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta ya requería inscripción.');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
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
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            if($rutaIns->getFechaNosocio() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha de inscripción de no socios de la ruta debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            if($rutaIns->getFechaSocio() > $ruta->getFecha()){
                $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de socios de la ruta debe ser anterior a la fecha de la ruta.');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            if($rutaIns->getFechaNosocio() > $ruta->getFecha()){
                $this->addFlash(type: 'error', message: 'ERROR:  La fecha de inscripción de no socios de la ruta debe ser anterior a la fecha de la ruta.');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            $em->persist($ruta);
            $em->persist($rutaIns);
            $em->flush();
            //REDIRECCIÓN
            $this->addFlash('exito','Ha habilitado la inscripción a la ruta correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        }

        return $this->render('administrador/registrarRutaInscripcion.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------
    //INSCRIBIRSE RUTA
    //-----------------

    #[Route('/administrador/ruta/inscripcion/{id}', name: 'inscripcionRutaAd')]
    public function inscripcionRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        $usuarioruta = new UsuarioRuta();
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId()); 


        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        }
        
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
        $form = $this->createForm(DatosRutaMostrarType::class, $rutaIns);
        $now = date_create("now");
        if($rutaIns->getFechaNosocio() > $now){
            $this->addFlash('info', 'Lo sentimos...Aún no se ha abierto el plazo de inscripción');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        }

        $inscrito = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
        if($inscrito !== null){
            $this->addFlash('info', 'Usted ya esta inscrito a esta ruta');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
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
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
        //REDIRECCIÓN
        }

        return $this->render('administrador/inscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }

    //-----------------------
    //BUSCAR USUARIOS INSCRITOS
    //-----------------------

    #[Route('/administrador/ruta/inscritos/{id}', name: 'usuariosInscritosAd')]
    public function inscritosRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rutaIns = new RutaConInscripcion();
        if(!$em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id))){
            $this->addFlash('info', 'Esta ruta no requiere inscripción previa');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
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

        return $this->render('administrador/usuariosInscritos.html.twig', [
            'controller_name' => 'Usuarios Inscritos',
            'usuarios' => $usuarios,
            'rutaI' => $rutaIns
        ]);
    }
    


    //-----------------
    //BUSCAR MIS RUTAS INSCRITAS
    //-----------------

    #[Route('/administrador/miactividad/rutas', name: 'rutasInscritasBuscarAdmin')]
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

        return $this->render('administrador/rutasInscritas.html.twig', [
            'controller_name' => 'Buscar una Ruta',
            'rutas' => $rutas
        ]);
}


    //-----------------
    //ASIGNAR RUTERO
    //-----------------

    #[Route('/administrador/rutas/asignarrutero/{id}', name: 'asignarRuteroAd')]
    public function asignarRuteroRuta(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioRutaType::class, $usuario);
        $rutaIns = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('ruta' => $id));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $usuario = $em->getRepository(Usuario::class)->findOneBy(array('email' => $form['email']->getData()));
            if($usuario == null){
                $this->addFlash('error', 'ERROR: Lo sentimos...Ese correo electrónico no existe...');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            $usuarioruta = $em->getRepository(UsuarioRuta::class)->findOneBy(array('id_usuario' => $usuario->getId(), 'id_ruta' => $rutaIns->getId()));
            if($usuarioruta == null){
                $this->addFlash('error', 'ERROR: Lo sentimos...Ese usuario no está inscrito en la ruta seleccionada...');
                return $this->redirectToRoute(route: 'rutaBuscarAdmin');
            }
            $usuarioruta->setRutero(true);
            $em->persist($usuarioruta);
            $em->flush();
            //Se crea la tupla del evento en el formulario
            $this->addFlash(type: 'exito', message: 'El rutero se ha asignado correctamente');
            return $this->redirectToRoute(route: 'rutaBuscarAdmin');
    }

    return $this->render('administrador/asignarRutero.html.twig', [
        'controller_name' => '',
        'form' => $form->createView()
    ]);

}

    //-----------------
    //ANULAR INSCRIPCION RUTA
    //-----------------

    #[Route('/administrador/miactividad/rutas/anularinscripcion/{id}', name: 'anularInscripcionRutaAd')]
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
            $this->addFlash('success', 'Inscripción anulada con éxito.');
            return $this->redirectToRoute(route: 'rutasInscritasBuscarAdmin');
        //REDIRECCIÓN
        }

        return $this->render('administrador/anularInscripcionRuta.html.twig', [
            'controller_name' => '',
            'form' => $form->createView()
        ]);
    }


    //-----------------
    //BUSCAR EVENTO
    //-----------------

    #[Route('/administrador/evento/buscar', name: 'buscarEventoAd')]
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
                return $this->render('administrador/eventosEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'eventos' => $temp
                ]);
    }



    //-----------------
    //CONSULTAR EVENTO
    //-----------------   

    #[Route('/administrador/evento/consultar/{id}', name: 'consultarEventoAd')]
    public function consultarEvento($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $evento = $em->getRepository(Evento::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(EventoType::class, $evento);

        return $this->render('administrador/consultarEvento.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
        ]);
    }

    //-----------------
    //EDITAR EVENTO
    //-----------------

    #[Route('/administrador/evento/editar/{id}', name: 'editarEventoAd')]
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
            return $this->redirectToRoute(route: 'eventoBuscarAdmin');
        }

        return $this->render('administrador/editarEvento.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //ELIMINAR EVENTO
    //-----------------

    #[Route('/administrador/evento/eliminar/{id}', name: 'eliminarEventoAd')]
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
            return $this->redirectToRoute(route: 'eventoBuscarAdmin');
        }
        
        return $this->render('administrador/eliminarEvento.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //REGISTRAR EVENTO
    //-----------------

    #[Route('/administrador/evento/registrar', name: 'AdministradorRegistrarEvento')]
    public function registrarEvento(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $evento = new Evento();
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            //Se guarda el evento en la base de datos
            $now = date_create("now");
            if($evento->getFecha() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha del evento debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'eventoBuscarAdmin');
            }
            $em->persist($evento);
            $em->flush();
            //Se crea la tupla del evento en el formulario
            $this->addFlash(type: 'exito', message: 'El evento se ha registrado correctamente');
            return $this->redirectToRoute(route: 'eventoBuscarAdmin');
        }

        return $this->render('administrador/registrarEvento.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }



    //-----------------
    //BUSCAR MATERIAL DEPORTIVO
    //-----------------

    #[Route('/administrador/materialdeportivo/buscar', name: 'buscarMaterialAd')]
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

                return $this->render('administrador/materialesEncontrados.html.twig', [
                    'controller_name' => 'Buscar un Evento',
                    'materiales' => $temp
                ]);
    }
    //-----------------
    //CONSULTAR MATERIAL
    //-----------------   

    #[Route('/administrador/materialdeportivo/consultar/{id}', name: 'consultarMaterialAd')]
    public function consultarMaterial($id): Response
    {
        $em = $this->getDoctrine()->getManager();

        //BÚSQUEDA DE EVENTO
        $material = $em->getRepository(MaterialDeportivo::class)->find($id);
        //CREACIÓN FORMULARIO
        $form = $this->createForm(MaterialDeportivoType::class, $material);

        return $this->render('administrador/consultarMaterial.html.twig', [
            'controller_name' => 'Datos de la consulta',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }

    //-----------------
    //EDITAR MATERIAL
    //-----------------

    #[Route('/administrador/materialdeportivo/editar/{id}', name: 'editarMaterialAd')]
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
                return $this->redirectToRoute(route: 'materialBuscarAdmin');
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
            return $this->redirectToRoute(route: 'materialBuscarAdmin');
        }
        
        return $this->render('administrador/editarMaterial.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }

    //-----------------
    //ELIMINAR MATERIAL
    //-----------------

    #[Route('/administrador/materialdeportivo/eliminar/{id}', name: 'eliminarMaterialAd')]
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
            return $this->redirectToRoute(route: 'materialBuscarAdmin');

        }

        return $this->render('administrador/eliminarMaterial.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView(),
            'img_material' => $material->getImagenPrenda()
        ]);
    }


    //-----------------------
    //BUSCAR USUARIOS SOLICITANTES
    //-----------------------

    #[Route('/administrador/materialdeportivo/solicitado/{id}', name: 'solicitudPrendaAd')]
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

        return $this->render('administrador/usuariosSolicitantesPrenda.html.twig', [
            'controller_name' => 'Usuarios Inscritos',
            'usuarios' => $usuarios,
            'material' => $material

        ]);
    }



    //-----------------
    //REGISTRAR MATERIAL
    //-----------------

    #[Route('/administrador/materialdeportivo/registrar', name: 'AdministradorRegistrarMaterial')]
    public function registrarMaterial(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $material = new MaterialDeportivo();
        $form = $this->createForm(MaterialDeportivoType::class, $material);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $now = date_create("now");
            if($material->getFechaLimite() < $now){
                $this->addFlash(type: 'error', message: 'ERROR: La fecha límite introducida debe ser posterior al día de hoy.');
                return $this->redirectToRoute(route: 'materialBuscarAdmin');
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



            $material->setFechaOferta(\DateTime::createFromFormat('Y-m-d',date('Y-m-d'))); 
            $em = $this->getDoctrine()->getManager();
            //Se guarda el material en la base de datos
            $em->persist($material);
            $em->flush();
            //Se crea la tupla del material en el formulario
            $this->addFlash(type: 'exito', message: 'El material se ha registrado correctamente');
            return $this->redirectToRoute(route: 'materialBuscarAdmin');
        }

        return $this->render('administrador/registrarMaterial.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }



    //-----------------
    //CONSULTAR TU PERFIL
    //-----------------


    #[Route('/administrador/perfil', name: 'consultarPerfilAdmin')]
    public function consultarPerfil(): Response
    {
        $em = $this->getDoctrine()->getManager();

        //DATOS DE USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());

        $form = $this->createForm(DatosType::class, $usuario);

        return $this->render('administrador/consultarPerfil.html.twig', [
            'controller_name' => 'Perfil del usuario',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //EDITAR TU PERFIL
    //-----------------


    #[Route('/administrador/perfil/editar/{id}', name: 'editarPerfilAdmin')]
    public function editarPerfil(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        //DATOS DE USUARIO
        $usuario = $em->getRepository(Usuario::class)->findOneBy(array('id' => $id));
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);
        $form->remove('roles');
        $form->remove('email');
        $output = new ConsoleOutput();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $output->writeln('45');
            $em->persist($usuario);
            $em->flush();
            $this->addFlash(type: 'success', message: 'Ha editado su perfil correctamente.');
            return $this->redirectToRoute(route: 'app_administrador');
        }

        return $this->render('administrador/editarPerfil.html.twig', [
            'controller_name' => '',
            'formulario' => $form->createView()
        ]);
    }


    //-----------------
    //ELIMINAR TU PERFIL
    //-----------------


    #[Route('/administrador/perfil/eliminar', name: 'eliminarPerfilAdmin')]
    public function eliminarPerfil(Request $request): Response
    {
        $usuario = new Usuario();
        $em = $this->getDoctrine()->getManager();

        //DATOS USUARIO
        $usuario = $em->getRepository(Usuario::class)->find($this->getUser()->getId());
        $form = $this->createForm(ConfirmarUsuarioType::class, $usuario);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $admin = $em->getRepository(Administrador::class)->findOneBy(array('usuario' => $this->getUser()->getId()));

            //CERRAMOS LA SESIÓN PARA EVITAR PROBLEMAS
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            //BORRAMOS
            $em->remove($admin);
            $em->remove($usuario);
            $em->flush();

            //Se cierra la sesión
            return $this->redirectToRoute(route: 'app_logout');
        }

        return $this->render('administrador/eliminarPerfil.html.twig', [
            'controller_name' => 'Esta es la página para borrar el perfil. CUIDADO',
            'formulario' => $form->createView()
        ]);
    }

    //-----------------
    //CAMBIAR CONTRASEÑA
    //-----------------
    
    #[Route('/administrador/perfil/contraseña', name: 'cambiarContraseñaAdmin')]
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
                return $this->redirectToRoute(route: 'app_administrador');
            }

            $this->addFlash(type: 'danger', message: 'La contraseña actual introducida es incorrecta');
            return $this->redirectToRoute(route: 'app_administrador');
        }

        return $this->render('administrador/cambiarContraseña.html.twig', array(
            'changePasswordForm' => $form->createView(),
        ));        
    }



    //-----------------
    //GENERAR PDF TODOS USUARIOS
    //-----------------
    
    #[Route('/administrador/usuario/informe', name: 'informeUsuarios')]
    public function informeUsuarios(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $usuarios = $em->getRepository(Usuario::class)->findBy(array(), array('apellidos' => 'ASC'));
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $usuarios
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosUsuarios.pdf", [
            "Attachment" => true
        ]);
    }

    //--------------------------------------------------
    //GENERAR PDF TODOS USUARIOS PARTICIPANTES A UNA RUTA
    //---------------------------------------------------
    
    #[Route('/administrador/ruta/inscritos/{id}/participantes', name: 'imprimirParticipantes')]
    public function informeParticipantes(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $rutaI = $em->getRepository(RutaConInscripcion::class)->findOneBy(array('id' => $id));
        $ruta = $em->getRepository(Ruta::class)->findOneBy(array('id' => $rutaI->getRuta()));
        $usuariosrutas = $em->getRepository(UsuarioRuta::class)->findBy(array('id_ruta' => $id));
        $usuarios = array();
        for($i = 0; $i < sizeof($usuariosrutas); $i++) {
            //De las rutas con inscripcion sacamos los datos de la ruta
            $usuario = $em->getRepository(Usuario::class)->findOneBy(array('id' => $usuariosrutas[$i]->getIdUsuario()));
            array_push($usuarios,$usuario);
         }
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $usuarios
        ]);
        $nombre = "listadoParticipantes-{$ruta->getNombre()}.pdf";
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODAS RUTAS
    //-----------------
    
    #[Route('/administrador/rutas/informe', name: 'informeRutas')]
    public function informeRutas(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $rutas = $em->getRepository(Ruta::class)->findAll();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaRutas.html.twig', [
            'controller_name' => '',
            'rutas' => $rutas
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodasRutas.pdf", [
            "Attachment" => true
        ]);
    }


    //-----------------
    //GENERAR PDF TODAS RUTAS INSCRIPCION
    //-----------------
    
    #[Route('/administrador/rutas/informeinscritas', name: 'informeRutasIns')]
    public function informeRutasIns(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $urutas = $em->getRepository(RutaConInscripcion::class)->findAll();
        $rutas = array();
        for($i = 0; $i < sizeof($urutas); $i++) {
           //De las rutas con inscripcion sacamos los datos de la ruta
           $ruta = $em->getRepository(Ruta::class)->findOneBy(array('id' => $urutas[$i]->getRuta()));
           array_push($rutas,$ruta);
        }
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaRutas.html.twig', [
            'controller_name' => '',
            'rutas' => $rutas
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodasRutasConInscripcion.pdf", [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODOS EVENTOS
    //-----------------
    
    #[Route('/administrador/eventos/informe', name: 'informeEventos')]
    public function informeEventos(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $eventos = $em->getRepository(Evento::class)->findAll();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaEventos.html.twig', [
            'controller_name' => '',
            'eventos' => $eventos
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosEventos.pdf", [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODOS MATERIALES
    //-----------------
    
    #[Route('/administrador/materialdeportivo/informe', name: 'informeMaterial')]
    public function informeMaterial(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $materiales = $em->getRepository(MaterialDeportivo::class)->findAll();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaMateriales.html.twig', [
            'controller_name' => '',
            'materiales' => $materiales
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosMaterialesDeportivos.pdf", [
            "Attachment" => true
        ]);
    }

    //--------------------------------------------------
    //GENERAR PDF TODOS USUARIOS SOLICITANTES DE UNA PRENDA
    //---------------------------------------------------
    
    #[Route('/administrador/materialdeportivo/solicitado/{id}/solicitantes', name: 'informeSolicitantes')]
    public function informeSolicitantes(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $material = $em->getRepository(MaterialDeportivo::class)->findOneBy(array('id' => $id));
        $sociosmaterial = $em->getRepository(SocioMaterialdeportivo::class)->findBy(array('id_material' => $id));
        $socios = array();
        for($i = 0; $i < sizeof($sociosmaterial); $i++) {
            //De las rutas con inscripcion sacamos los datos de la ruta
            $socio = $em->getRepository(Socio::class)->findOneBy(array('id' => $sociosmaterial[$i]->getIdUsuario()));
            $usuario = $em->getRepository(Usuario::class)->findOneBy(array('id' => $socio->getUsuario()));
            array_push($socios,$usuario);
         }
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $socios
        ]);
        $nombre = "listadoSolicitantes-{$material->getNombre()}.pdf";
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODOS SOCIOS
    //-----------------
    
    #[Route('/administrador/usuario/informeSocio', name: 'informeSocios')]
    public function informeSocios(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $usuarios = $em->getRepository(Usuario::class)->findAll();
        $socios = array();
        for($i = 0; $i < sizeof($usuarios); $i++) {
            if($usuarios[$i]->esSocio()){
                array_push($socios,$usuarios[$i]);
            }
         }
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $socios
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosSocios.pdf", [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODOS CONSULTORES
    //-----------------
    
    #[Route('/administrador/usuario/informeConsultor', name: 'informeConsultores')]
    public function informeConsultores(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $usuarios = $em->getRepository(Usuario::class)->findAll();
        $consultores = array();
        for($i = 0; $i < sizeof($usuarios); $i++) {
            if($usuarios[$i]->esConsultor()){
                array_push($consultores,$usuarios[$i]);
            }
         }
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $consultores
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosConsultores.pdf", [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODOS CONSULTORES
    //-----------------
    
    #[Route('/administrador/usuario/informeEditores', name: 'informeEditores')]
    public function informeEditores(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $usuarios = $em->getRepository(Usuario::class)->findAll();
        $editores = array();
        for($i = 0; $i < sizeof($usuarios); $i++) {
            if($usuarios[$i]->esEditor()){
                array_push($editores,$usuarios[$i]);
            }
         }
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $editores
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosEditores.pdf", [
            "Attachment" => true
        ]);
    }

    //-----------------
    //GENERAR PDF TODOS CONSULTORES
    //-----------------
    
    #[Route('/administrador/usuario/informeAdministradores', name: 'informeAdministradores')]
    public function informeAdministradores(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $usuarios = $em->getRepository(Usuario::class)->findAll();
        $administradores = array();
        for($i = 0; $i < sizeof($usuarios); $i++) {
            if($usuarios[$i]->esAdministrador()){
                array_push($administradores,$usuarios[$i]);
            }
         }
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrador/listaUsuarios.html.twig', [
            'controller_name' => '',
            'usuarios' => $administradores
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("listadoTodosAdministradores.pdf", [
            "Attachment" => true
        ]);
    }

}



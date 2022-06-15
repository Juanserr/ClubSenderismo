<?php

namespace App\Controller;

use App\Form\UserType;
use App\Form\SocioType;
use App\Form\ConsultorType;
use App\Form\AdministradorType;
use App\Form\EditorType;
use App\Entity\Usuario;
use App\Entity\Consultor;
use App\Entity\Administrador;
use App\Entity\Socio;
use App\Entity\Editor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistroController extends AbstractController
{
    #[Route('/registro', name: 'app_registro')]
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $usuario = new Usuario();
        $socio = new Socio();
        $consultor = new Consultor();
        $editor = new Editor();
        $administrador = new Administrador();
        $form = $this->createForm(UserType::class, $usuario);
        $formS = $this->createForm(SocioType::class, $socio);
        $formC = $this->createForm(ConsultorType::class, $consultor);
        $formE = $this->createForm(EditorType::class, $editor);
        $formA = $this->createForm(AdministradorType::class, $administrador);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            //Fecha de Registro
            $usuario->setFechaalta(\DateTime::createFromFormat('Y-m-d h:i:s',date('Y-m-d h:i:s')));
            //Codificación
            $usuario->setPassword($passwordEncoder->encodePassword($usuario,$form['password']->getData()));
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
            return $this->redirectToRoute(route: 'app_dashboard');
        }

        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView(),
            'formC' => $formC->createView(),
            'formE' => $formE->createView(),
            'formS' => $formS->createView()
        ]);
    }
}

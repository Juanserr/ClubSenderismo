<?php

namespace App\Controller;
use App\Entity\Ruta;
use App\Form\RutaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RutaController extends AbstractController
{
    #[Route('/Registrar-ruta', name: 'RegistrarRuta')]
    public function index(Request $request): Response
    {
        $ruta = new Ruta();
        $form = $this->createForm(RutaType::class, $ruta);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($ruta);
            $em->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('ruta/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/Ruta/{id}', name: 'verRuta')]
    public function verRuta($id){
        $em = $this->getDoctrine()->getManager();
        $ruta = $em->getRepository(Ruta::class)->find($id);
        return $this->render('ruta/verRuta.html.twig', ['ruta'=>$ruta]);
    }

    #[Route('/nombreruta', name: 'app_dashboard')]
    public function verRutaporNombre(){
        $em = $this->getDoctrine()->getManager();
        $ruta = $em->getRepository(Ruta::class)->findBy(['nombre'=>'Tendillas']);
        return $this->render('ruta/verRutaporNombre.html.twig', ['ruta'=>$ruta]);
    }
}


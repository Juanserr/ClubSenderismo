<?php

namespace App\Controller;

use App\Entity\Evento;
use App\Form\EventoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
class EventoController extends AbstractController
{
    #[Route('/Registrar-evento', name: 'RegistrarEvento')]
    public function index(Request $request): Response
    {
        $evento = new Evento();
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($evento);
            $em->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('evento/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

<?php

namespace App\Controller;
use App\Entity\Ruta;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $output = new ConsoleOutput();
        /*
        $em = $this->getDoctrine()->getManager();
        //$rutas = $em->getRepository(Ruta::class)->findAll();
        $query = $em->getRepository(Ruta::class)->buscarTodasRutas();
        //$ruta = $em->getRepository(Ruta::class)->find(1);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
          //  $request->query->getInt('page', 1), /*page number*/
            //1 /*limit per page*/
        //);
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'Esta es la página principal',
        ]);
    }

    #[Route('/rutaBuscar', name: 'rutaBuscarPublico')]
    public function rutaBuscar(Request $request): Response
    {
        $rutas = "";

        return $this->render('dashboard/buscarRuta.html.twig', [
            'controller_name' => 'Esta es la página para buscar una Ruta',
            'rutas' => $rutas
        ]);
    }

    #[Route('/eventoBuscar', name: 'eventoBuscarPublico')]
    public function eventoBuscar(Request $request): Response
    {
        $eventos = "";

        return $this->render('dashboard/buscarEvento.html.twig', [
            'controller_name' => 'Esta es la página para buscar un Evento',
            'eventos' => $eventos
        ]);
    }

    #[Route('/materialBuscar', name: 'materialBuscarPublico')]
    public function materialBuscar(Request $request): Response
    {
        $material = "";

        return $this->render('dashboard/buscarMaterial.html.twig', [
            'controller_name' => 'Esta es la página para buscar Material Deportivo',
            'material' => $material
        ]);
    }

    #[Route('/ayuda', name: 'ayudaPublico')]
    public function ayudaUsuarioPublico(): Response
    {
        return $this->render('principal/ayuda.html.twig', [
            'controller_name' => 'Esta es la página de ayuda para el Usuario Público',
        ]);
    }



}

<?php

namespace App\Controller;
use App\Entity\MaterialDeportivo;
use App\Form\MaterialDeportivoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class MaterialDeportivoController extends AbstractController
{
    #[Route('/Registrar-materialdeportivo', name: 'RegistrarMaterialDeportivo')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $material = new MaterialDeportivo();
        $form = $this->createForm(MaterialDeportivoType::class, $material);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('imagen_prenda')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('fotos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error...');
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $material->setImagenPrenda($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $material->setFechaOferta(\DateTime::createFromFormat('Y-m-d h:i:s',date('Y-m-d h:i:s')));
            $em->persist($material);
            $em->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('material_deportivo/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}


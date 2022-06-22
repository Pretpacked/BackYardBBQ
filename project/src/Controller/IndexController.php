<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Barbecue;
use App\Form\BarbecueImportType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        

        return $this->render('index/index.html.twig');
    }

    #[Route('/api/orders', name: 'apiOrders')]
    public function apiOrders(ManagerRegistry $doctrine): Response
    {
        return new JsonResponse(['data' => $this->container->get('serializer')->serialize(
            $doctrine->getRepository(Barbecue::class)->findAll(), 'json')]);
    }

    #[Route('/api/remove/{id}', name: 'apiRemoveBarbecue')]
    public function apiRemoveBarbecue(ManagerRegistry $doctrine)
    {

    }

    #[Route('/orders', name: 'orders')]
    public function orders(): Response
    {
        return $this->render('index/orders.html.twig');
    }

    #[Route('/huren', name: 'huren')]
    public function huren(): Response
    {
        return $this->render('index/huren.html.twig');
    }

    // function for importing new barbecue's into the website
    #[Route('/admin/import/barbecue', name: 'import_bqq')]
    public function import_bqq(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        // getting the entity object and handling the form request
        $barbecue = new Barbecue();
        $form = $this->createForm(BarbecueImportType::class, $barbecue);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $barbecue->setImage($newFilename);
            }

            $barbecue->setName($form->get('name')->getData());
            $barbecue->setBarbecuePrice($form->get('barbecue_price')->getData());
            $barbecue->setDescription($form->get('description')->getData());

            $entityManager->persist($barbecue);
            $entityManager->flush();


            return $this->redirectToRoute('index');
        }

        // return the form object ad load the template
        return $this->renderForm('forms/barbecue_import.html.twig', [
            'form' => $form,
        ]);

    }
  
}

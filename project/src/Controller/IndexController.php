<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Barbecue;
use App\Form\BarbecueImportType;
use Symfony\Component\String\Slugger\SluggerInterface;


class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }
    #[Route('/huren', name: 'huren')]
    public function huren(): Response
    {
        return $this->render('index/huren.html.twig');
    }

    #[Route('/admin/import/barbecue', name: 'import_bqq')]
    public function import_bqq(Request $request, SluggerInterface $slugge): Response
    {
        if (!$this->denyAccessUnlessGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('index');
        }

        $barbecue = new Barbecue();
        $form = $this->createForm(BarbecueImportType::class, $barbecue);
        $form->handleRequest($request);

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
                        $this->getParameter('image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $barbecue->setBrochureFilename($newFilename);
            }

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('index');
        }

        return $this->renderForm('forms/barbecue_import.html.twig', [
            'form' => $form,
        ]);

    }
  
}

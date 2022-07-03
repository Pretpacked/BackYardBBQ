<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Barbecue;
use App\Entity\Accessoire;
use App\Form\BarbecueImportType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Customer;
use App\Form\CheckoutType;
use App\Entity\Order;


class IndexController extends AbstractController
{
    // showing the home page.
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    // json get for the admin table, shows a table of all bbq in the system.
    #[Route('/api/orders', name: 'apiOrders')]
    public function apiOrders(ManagerRegistry $doctrine): Response
    {
        return new JsonResponse(['data' => $this->container->get('serializer')->serialize(
            $doctrine->getRepository(Barbecue::class)->findAll(), 'json')]);
    }

    #[Route('/api/remove/{id}', name: 'apiRemoveBarbecue')]
    public function apiRemoveBarbecue(ManagerRegistry $doctrine)
    {
        // TODO
    }

    // add accessoire to cart, redirect to checkout page.
    #[Route('/cart/add/{id}', name: 'addAccessoire')]
    public function addAccessoire(ManagerRegistry $doctrine, $id, Request $request)
    {
        $session = $request->getSession();

        if(isset($session) AND $session->has('cart_accessoire')){
            $session->set('cart_accessoire', $id);
        }
        return $this->redirect('/checkout');
    }

    #[Route('/checkout', name: 'checkout')]
    public function checkout(ManagerRegistry $doctrine, Request $request)
    {
        $customer = new Customer;
        $order = new Order;

        $form = $this->createForm(CheckoutType::class, $customer);
        $form->handleRequest($request);

        $session = $request->getSession();
        $cart = ($session->has('cart_bbq')) ? $session->get('cart_bbq') : NULL; 
        $accessoire = ($session->has('cart_accessoire')) ? $session->get('cart_accessoire') : NULL; 
        $total = 0;
        $bbqOrder = array();
    
        if($cart == null){
            return $this->render('index');
        }
   
        if($form->isSubmitted() AND $form->isValid()){

            $customer->setName($form->get('name')->getData());
            $customer->setAdress($form->get('adress')->getData());
            $customer->setPhoneNumber($form->get('phone_number')->getData());

            $order->setCustomer($customer);
            $order->setAccessoires($accessoire);
            for ($i=0; $i < count($cart); $i++) { 
                $order->setBarbecue($cart[$i]);
            }
            $order->setOrderdDate();
            $order->setStartDate();
            $order->setEndDate();
            $order->setPriceTotal();
            $order->setRemark();

            $entityManager->persist($customer);
            $entityManager->persist($order);
            $entityManager->flush();
        }

        for ($i=0; $i < count($cart); $i++) { 
            $bbq = $doctrine->getRepository(Barbecue::class)->find((string)$cart[$i]);
            $total += $bbq->getBarbecuePrice();

            array_push($bbqOrder, $bbq);
        };
        return $this->renderForm('index/checkout.html.twig', array(
            'form' => $form, 
            'data' => $bbqOrder,
            'btw' => ($total / 100 * 21),
            'total' => ($total + ($total / 100 * 21))
        ));
    }

      // remove bbq from cart
      #[Route('/cart/remove/{id}', name: 'cartRemove')]
      public function cartRemove(Request $request, ManagerRegistry $doctrine, $id)
      {
        $session = $request->getSession();
        $cart = $session->get('cart_bbq');

        unset($cart[array_search($id, $cart)]);
        array_values($cart);
        $session->set('cart_bbq', $cart);
        return new JsonResponse([]);
      }

    // clearing cart.
    #[Route('/cart/clear', name: 'cartClear')]
    public function cartClear(Request $request, ManagerRegistry $doctrine)
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirect('/huren');
    }

    // function for showing the cart.
    #[Route('/cart/get', name: 'showNumberInCart')]
    public function showNumberInCart(Request $request, ManagerRegistry $doctrine)
    {
        $session = $request->getSession();

        // check if there is anything in cart other wish send null.
        if(isset($session) AND $session->has('cart_bbq')){
            return new JsonResponse(['data' => $session->get('cart_bbq')]);
        }else{
            return new JsonResponse(['data' => null]);
        }
    }

    // function for adding bbq to the cart
    #[Route('/bqq/add/cart/{id}', name: 'addBqqCart')]
    public function addBqqCart(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = $request->getSession();

        // check if session isset if not create it.
        if(!$session->has('cart_bbq')){
            $session->start();

            $session->set('cart_bbq', array($id));
        
        }else{
            $array = $session->get('cart_bbq');

            // check if the inputted bbq isn't duplicate.
            if(end($array) !== $id){
                array_push($array, $id);
                $session->set('cart_bbq', $array);
            }
        }
        // dd($session->has('cart_accessoire'));
        if($session->has('cart_accessoire')){
            return $this->redirect('checkout');
        }

        return $this->render('index/add_accessoires.html.twig', array('data'=> $doctrine->getRepository(Accessoire::class)->findAll()));
    }

    // showing bbq overview page.
    #[Route('/bqq/overview/{id}', name: 'showBarbecueOverview')]
    public function showBarbecueOverview(ManagerRegistry $doctrine, $id)
    {
        return $this->render('index/overviewBarbecue.html.twig', array('data'=> $doctrine->getRepository(Barbecue::class)->findBy(array('id' => (int)$id))));
    }

    // showing admin order page.
    #[Route('/orders', name: 'orders')]
    public function orders(): Response
    {
        return $this->render('index/orders.html.twig');
    }

    // show bbq select page.
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

            // check if there is a file uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // making the file name url safe
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images'),
                        $newFilename
                    );
                } catch (FileException $e) {}

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

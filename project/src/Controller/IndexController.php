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
use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class IndexController extends AbstractController
{
    // showing the home page.
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    // showing the contact page.
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()){
            $contact->setName($form->get('name')->getData());
            $contact->setEmail($form->get('email')->getData());          
            $contact->setSubject($form->get('subject')->getData());     
            $contact->setMessage($form->get('message')->getData());                

            $entityManager = $doctrine->getManager();

            $entityManager->persist($contact);
            $entityManager->flush();

            $email = (new Email())
            ->from('ricardobettonvil@gmail.com')
            ->to($form->get('email')->getData())
            ->subject($form->get('subject')->getData())
            ->text($form->get('message')->getData());

        $mailer->send($email);
        }
        return $this->renderForm('forms/contact.html.twig', array(
            'form' => $form
        ));
    }

    // json get for the admin table, shows a table of all bbq in the system.
    #[Route('/api/orders', name: 'apiOrders')]
    public function apiOrders(ManagerRegistry $doctrine): Response
    {    
        $response = new JsonResponse(['data' => $this->container->get('serializer')->serialize(
            $doctrine->getRepository(Barbecue::class)->findAll(), 'json', ['groups' => ['huren']])]);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
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
        $session->set('cart_accessoire', $id);
        return $this->redirectToRoute('checkout');
    }

    // showing the checkout page with form attached
    #[Route('/checkout', name: 'checkout')]
    public function checkout(ManagerRegistry $doctrine, Request $request)
    {
        $customer = new Customer;
        $order = new Order;

        $form = $this->createForm(CheckoutType::class, $customer);
        $form->handleRequest($request);

        // get session and check if cart/accessoire variable are filled
        $session = $request->getSession();
        $cart = ($session->has('cart_bbq')) ? $session->get('cart_bbq') : NULL; 
        $accessoire = NULL;
        $accessoirePrice = 0;
        $entityManager = $doctrine->getManager();
        $total = 0;
        $bbqOrder = array();

        // check if cart has accessoire
        if($session->has('cart_accessoire')){
            $accessoire = $doctrine->getRepository(Accessoire::class)->find((int)$session->get('cart_accessoire'));
            $accessoirePrice = $accessoire->getPrice();
        }

        // check if cart is filled
        for ($i=0; $i < count($cart); $i++) {
            // get bbq by id and push it into an array
            $bbq = $doctrine->getRepository(Barbecue::class)->find((int)$cart[$i]);
            $price = (int)$bbq->getBarbecuePrice();

            $total += $price;

            array_push($bbqOrder, $bbq);
        };

        // if empty redirect to index
        if(empty($bbqOrder)){
            
            return $this->redirectToRoute('index');
        }

        // check if submitted and valid, so yes throw all information into the database
        // and send it into the delivery page for view.
        if($form->isSubmitted() AND $form->isValid()){

            // fill customer entity
            $customer->setName($form->get('name')->getData());
            $customer->setAdress($form->get('adress')->getData());
            $customer->setPhoneNumber($form->get('phone_number')->getData());

            $order->setCustomer($customer);

            // check if accessoire is null
            if($accessoire !== null){
                $order->addAccessoire($accessoire);
                $accessoirePrice = $accessoire->getPrice();
            }
            
            // link the related bbq with the order
            for ($i=0; $i < count($cart); $i++) { 
                $bbq = $doctrine->getRepository(Barbecue::class)->find((int)$cart[$i]);
                $order->addBarbecue($bbq);
            }

            // fill order entity
            $order->setOrderdDate(new \DateTime(date('Y-m-d H:i:s')));
            $order->setStartDate($form->get('start_date')->getData());
            $order->setEndDate($form->get('end_date')->getData());
            $order->setPriceTotal(($total + ($total / 100 * 21) +  $accessoirePrice));
            if($form->get('remark')->getData() !== NULL){
                $order->setRemark($form->get('remark')->getData());

            }
            $order->setDelivery($form->get('delivery')->getData());

            $entityManager->persist($customer);
            $entityManager->persist($order);
            $entityManager->flush();

            // clear session and fill the order information
            $session = $request->getSession();
            $session->clear();
            $session->set('order_done', $bbqOrder);
            $session->set('order_accessoire_done', $accessoire);
            $session->set('accessoire_price', $accessoirePrice);
            $session->set('order_customer', array(
                'name' => $form->get('name')->getData(),
                'address' => $form->get('adress')->getData(),
                'phone_number' => $form->get('phone_number')->getData(),
                'orderd_date' => date('Y-m-d H:i:s'),
                'start_date' => $form->get('start_date')->getData()->format('Y-m-d'),
                'end_date' => $form->get('end_date')->getData()->format('Y-m-d'),
                'total' => $total,
                'delivery' => $form->get('delivery')->getData()
            ));

            return $this->redirectToRoute('order_information', array(
                'data' => $session->get('order_done'),
                'customer' => array(
                    'name' => $form->get('name')->getData(),
                    'address' => $form->get('adress')->getData(),
                    'phone_number' => $form->get('phone_number')->getData(),
                    'orderd_date' => date('Y-m-d H:i:s'),
                    'start_date' => $form->get('start_date')->getData()->format('Y-m-d'),
                    'end_date' => $form->get('end_date')->getData()->format('Y-m-d'),
                    'delivery' => $form->get('delivery')->getData()
                ),
                'accessoire' =>  $session->get('order_accessoire_done'),
                'btw' => ($total / 100 * 21),
                'total' => ($total + ($total / 100 * 21) +  $accessoirePrice)
            ));
        }

        return $this->renderForm('index/checkout.html.twig', array(
            'form' => $form, 
            'data' => $bbqOrder,
            'accessoire' => $accessoire,
            'btw' => ($total / 100 * 21),
            'total' => ($total + ($total / 100 * 21) + $accessoirePrice)
        ));
    }

    // Laat de bezorg informatie voor de klant zien.
    #[Route('order/information', name: 'order_information')]
    public function orderInformation(ManagerRegistry $doctrine, Request $request){
        $session = $request->getSession();

        return $this->render('index/checkout_complete.html.twig', array(
            'data' => $session->get('order_done'),
            'customer' => array(
                'name' => $session->get('order_customer')['name'],
                'address' => $session->get('order_customer')['address'],
                'phone_number' => $session->get('order_customer')['phone_number'],
                'orderd_date' => $session->get('order_customer')['orderd_date'],
                'start_date' => $session->get('order_customer')['start_date'],
                'end_date' => $session->get('order_customer')['end_date'],
                'delivery' => $session->get('order_customer')['delivery']

            ),
            'accessoire' =>  $session->get('order_accessoire_done'),
            'btw' => ($session->get('order_customer')['total'] / 100 * 21),
            'total' => ($session->get('order_customer')['total'] + ($session->get('order_customer')['total'] / 100 * 21) + $session->get('accessoire_price'))
        ));
    }

    // remove bbq from cart
    #[Route('/cart/remove/{id}', name: 'cartRemove')]
    public function cartRemove(Request $request, ManagerRegistry $doctrine, $id)
    {
        $session = $request->getSession();
        $cart = $session->get('cart_bbq');

        if(count($cart) === 0){
            $session->clear();
            return new JsonResponse([]);
        }

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

        return $this->redirectToRoute('huren');
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
            return $this->redirectToRoute('checkout');
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
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->render('index/orders.html.twig');
        }
        return $this->redirectToRoute('index');
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
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect('/');
        }
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
            $barbecue->setBarbecuePrice((int) $form->get('barbecue_price')->getData());
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

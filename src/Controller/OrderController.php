<?php

namespace App\Controller;

use App\Controller\Classes\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager = $entityManager;  
    }
    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {   
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('app_address_add');
        }
        //Permet d'accéder au valeur présente dans l'objet user
        // dd($this->getUser()->getAddresses()->getValues());
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_recap')]
    public function add(Request $request, Cart $cart): Response
    {   
        
        //Permet d'accéder au valeur présente dans l'objet user
        // dd($this->getUser()->getAddresses()->getValues());
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Enregistrer ma commande

            // On récupére la date du jour dans un objet
            $date = new DateTimeImmutable();

            // On récupére les données dans l'objet carriers
            $carriers = $form->get('carriers')->getData();
            

            // On récupére les données dans l'objet addresses
            $delivery = $form->get('addresses')->getData();
            
            // On forme dans la variable delivery_content les informations à récupérer pour la livraison
            $delivery_content = $delivery->getFirstname() . ' ' .  $delivery->getLastname();;
            $delivery_content .= '<br/>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $delivery_content .= "<br/>" . $delivery->getCompany();
            }

            $delivery_content .= "<br/>".$delivery->getAddress();
            $delivery_content .= "<br/>".$delivery->getPostal() . ' ' .  $delivery->getCity();
            $delivery_content .= "<br/>".$delivery->getCountry();
            
            //on instancie un nouvel objet Order (Order())
            $order = new Order();
            
            // On crée une référence propre à chaque commande en insérant la date ainsi qu'un id unique
            $reference = $date->format('dmy'). '-' . uniqid();

            // On insére la référence dans la commande
            $order->setReference($reference);
           
            // Il faut set un User pour lui attribuer la commande, on récupére l'utilisateur en cours avec la méthode (getUser())
            $order->setUser($this->getUser());
            
            // On envoie la date de création de la commande en récupérant l'objet créer au dessus
            $order->setCreatedAt($date);
            
            // On set le nom du transporteur de l'objet carriers en cours
            $order->setCarrierName($carriers->getName());
            
            // On set le prix du transporteur de l'objet carriers en cours
            $order->setCarrierPrice($carriers->getPrice());
            
            // On set les données sur les coordonnées de livraison
            $order->setDelivery($delivery_content);

            // On vérifie si la commande est payé
            $order->setState(0);

            $this->entityManager->persist($order);
            
            // Enregistrer mes produits (OrderDetails())                     
            // Boucle pour récupérer chaque produit
            
            foreach($cart->getFull() as $product){
                
                // On instancie la classe OrderDetails
                $orderDetails = new OrderDetails();
                
                // On set la commande($order) dans OrderDetails
                $orderDetails->setMyorder($order);
                
                // On récupére le nom du produit commandé
                $orderDetails->setProduct($product['product']->getName());
                
                // On récupére la quantité de produit commandé
                $orderDetails->setQuantité($product['quantity']);
               
                // On récupére le prix des articles 
                $orderDetails->setPrice($product['product']->getPrice());

                // On récupére le total de la commande
                $orderDetails->setTotal($product['product']->getPrice()* $product['quantity']);

                $this->entityManager->persist($orderDetails);
            }
            
            $this->entityManager->flush();
          
            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $reference
                                        
            ]);
            
          

        return $this->redirectToRoute('app_cart');      
      }
   }
}

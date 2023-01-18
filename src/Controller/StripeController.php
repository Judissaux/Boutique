<?php

namespace App\Controller;

use App\Controller\Classes\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;



class StripeController extends AbstractController
{
    #[Route('/commande/create_session/{reference}', name: 'app_stripe')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference)
    {


        header('Content-Type: application/json');
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        if(!$order){
            return $this->redirectToRoute('app_order');

        }
        
        Stripe::setApiKey('sk_test_51MOzddCLIQ16f1TNBwBU5FkDVs3CHHFooU1AekOOHhfDBjorUBD7nCBREUX8m7KxFhD434BAME1PUTwtKxpv8z2G00WYRukiSR');


        foreach($order->getOrderDetails()->getValues() as $product){
        $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            
        $product_for_stripe [] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $product->getPrice(),
                'product_data' => [
                    'name' => $product->getProduct(),
                    'images' => [$YOUR_DOMAIN."/uploads/". $product_object->getIllustration()],
                ],
            ],

            'quantity' => $product->getQuantité(),

            ];  
        }

        $product_for_stripe [] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],

            'quantity' => 1,

            ];  
        
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);   
            
         $order->setStripeSessionId($checkout_session->id);
         $entityManager->flush();

         return $this->redirect($checkout_session->url);         
            
    }          
}

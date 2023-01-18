<?php

namespace App\Controller;

use App\Controller\Classes\Cart;
use App\Controller\Classes\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class OrderSuccessController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index(Cart $cart,$stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            $this->redirectToRoute('home');
        }
        
        if($order->getState() === 0){
            // On vide le panier de l'utilisateur
            $cart->remove();
            // On passe le statut isPaid à 1            
            $order->setState(1);
          
            $this->entityManager->flush();

              //On envoie un mail de confirmation 
              $mail = new mail();
              $content = "Bonjour ". $order->getUser()->getFirstname(). ", <br>
              Toute l'équipe de la Boutique Française vous remercie pour votre commande.";
              $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Bienvenue sur la Boutique Française',$content);              

        }
        
        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}

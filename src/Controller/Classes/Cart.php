<?php

namespace App\Controller\Classes;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
class Cart
{   
    //Variable pour récupérer les élements de la session utilisateur
    private $session;

    private $entityManager;

    //Interface session qui permet d'utiliser les méthodes lié à la session
    public function __construct(EntityManagerInterface $entityManager , SessionInterface $session)
    {
        // Permet de rendre l'interface accéssible via la class
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);
        
        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }

        $this->session->set('cart',$cart);
        
    }

    public function get()
    {
       return  $this->session->get('cart');
    }

     public function remove()
    {
       return  $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart',$cart);;
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart',[]);
        //On vérifie si le produit est supérieur à 1

        if($cart[$id]> 1){
        //On retire un produit
        $cart[$id]--; 
        }else{    
        // On efface le produit
        unset($cart[$id]);
        }      
             
        $this->session->set('cart', $cart);
    }

    public function getFull()
    {
        $cartComplete = [];
        $cart = $this->session->get('cart',[]);
        if(!empty($cart)){
                  
        foreach($cart as $id => $quantity){
        $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
       
        if(!$product_object){
            $this->delete($id);
            continue;
        }else{        
            $cartComplete[] = [
                'product' => $product_object,
                'quantity' => $quantity
            ];
          }
         
        }
     } 
        
        return $cartComplete;
    }
}
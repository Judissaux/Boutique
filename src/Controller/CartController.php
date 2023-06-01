<?php

namespace App\Controller;

use App\Controller\Classes\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class CartController extends AbstractController
{
    
        /**
     * @Route("/mon-panier", name="app_cart")
     */
    public function index(Cart $cart)
    {
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }

    
    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    // Fonction pour ajouter un article on a besoin de l'id produit pour ajouter
    public function add(Cart $cart, $id)
    {
        
        $cart->add($id);
               
        return $this->redirectToRoute('app_cart');
    }


    /**
     * @Route("/cart/remove", name="remove_cart")
     *
     * @param Cart $cart
     * @return void
     */
    public function remove(Cart $cart)
    {
        $cart->remove();
        return $this->redirectToRoute('app_products');
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_to_cart")
     *
     * @param Cart $cart
     * @return void
     */
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     */
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);

        return $this->redirectToRoute('app_cart');
    }
}

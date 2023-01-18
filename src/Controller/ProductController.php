<?php

namespace App\Controller;

use App\Controller\Classes\Search;
use App\Form\SearchType;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(entityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;  
    }
    
    /**
     * @Route("/nos-produits", name="app_products")
     */
    public function index(Request $request): Response
    {

        // Instance de la classe Search
        $search = new Search();
        
        // On crée le formulaire, on va le chercher (SearchType, et on utilise l'instance créer)
        $form = $this->createForm(SearchType::class,$search);
        
        $form = $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){          
            //Permet de récupérer les produits filtré 
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else{
            //Permet de récupérer les produits présents dans la BDD
            $products= $this->entityManager->getRepository(Product::class)->findAll();
        }
        
        return $this->render('product/index.html.twig',[
                                'products' => $products,
                                'form' => $form->createView(),
        ]);
    }

   /**
    * @Route("/produit/{slug}", name="app_product")
    *
    * @param string $slug
    * @return void
    */
    public function show($slug)
    {
        
        $product= $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        if(!$product){
            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/show.html.twig', [                                
                                'product' => $product,
                                'products' => $products
                                
        ]);
    }
}

<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $form= $this->createForm(ContactType::class);
        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('notice', 'Votre message a bien été envoyé, notre équipe vous répondra dans les meilleurs délais.');
        }
        return $this->render('contact/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}

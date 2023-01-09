<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AccountAddressController extends AbstractController
{   
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;    
    }
    #[Route('/compte/addresses', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'app_address_add')]
    public function add(Request $request): Response
    {
        // On instancie la class l'entité Address
        $address = new Address;

        // On crée le formulaire en récupérant la class AdressType et la class instancié
        $form = $this->createForm(AddressType::class,$address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_address');
        }

        // On crée la vue
        return $this->render('accounform.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_address_edit')]
    public function edit(Request $request, $id): Response
    {
        // On instancie la class l'entité Address
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_account_address');
        }
        // On crée le formulaire en récupérant la class AdressType et la class instancié
        $form = $this->createForm(AddressType::class,$address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                        
            $this->entityManager->flush();

            return $this->redirectToRoute('app_account_address');
        }

        // On crée la vue
        return $this->render('account/address_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'app_address_delete')]
    public function delete(Request $request, $id): Response
    {
        // On instancie la class l'entité Address
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() === $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }
        // On crée le formulaire en récupérant la class AdressType et la class instancié
        $form = $this->createForm(AddressType::class,$address);


            return $this->redirectToRoute('app_account_address');
        }

    }
}


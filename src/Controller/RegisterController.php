<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

//Renvoie la vue Inscription
class RegisterController extends AbstractController
{

    //Convention de nom pour créer la variable pour appeler doctrine
    private $entityManager;

    // Injection de dépendance de la class de doctrine permettant d'enregistrer les données en bdd (Dialogue avec la BDD)
    public function __construct(EntityManagerInterface $entityManager)
    {   
       $this->entityManager = $entityManager; 
    }
    
    /**
     * @Route("/inscription", name="app_register")
     */

     // Injection de dépendance en lui passant l'objet Request qui permet de récupérer les données du $_POST et injection de l'USERPASSWORDHASHER qui va nous permettre de hasher le mdp récupérer
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {

        // Ici j'instancie ma classe $user présente dans le dossier Entity
        $user = new User();
        
        // Ici on instancie le formulaire avec la méthode createForm avec 2 propriétés en paramétres, on injecte dans un premier temps la classe du formulaire (RegisterType présente dans le dossier Form), le second paramétre c'est les données présente dans la classe User, ici la variable instancié au dessus $user
        $form = $this->createForm(RegisterType::class,$user);
        
        // Ecouteur (handleRequest) qui permet de lire l'objet requête (Permet de savoir si il y a un $_POST qui a été envoyé)
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()){
            // On rappelle l'objet $user et on lui injecte toute les donnée récupérer via la méthode getData()
            $user = $form->getData();

            // On récupére le password avec la méthode getPassword() et on utilise la méthoe hashPassword acquise grâce à l'interface UserPasswordHasherInterface qui prends 2 paramétres , l'objet et le mot de passe
            $password = $encoder->hashPassword($user,$user->getPassword());

            // Ici on réinjecte le password hasher dans l'objet
            $user = $user->setPassword($password);

            // Méthode propre à l'ORM Doctrine qui prends un objet en paramètre(fonctionnement : fige les données et les prépare à étre créer en BDD)
            $this->entityManager->persist($user);

            //Méthode permettant d'enregistrer les donnée de persist() dans la base de données
            $this->entityManager->flush();   

            }
        

        // route pour accéder au formualire d'inscription et on crée la clef 'form' et on l'associe à la variable précédement créée $form en appelant également pour créer la vue la méthode createView()
        return $this->render('register/index.html.twig',['form' =>$form->createView()]);
    }
}

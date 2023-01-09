<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{

     //Convention de nom pour créer la variable pour appeler doctrine
     private $entityManager;

     // Injection de dépendance de la class de doctrine permettant d'enregistrer les données en bdd (Dialogue avec la BDD)
     public function __construct(EntityManagerInterface $entityManager)
     {   
        $this->entityManager = $entityManager; 
     }
    /**
     * @Route("/compte/modifier-mot-de-passe", name="app_account_password")
     */
    // Appel de l'interface 'Request permettant de récupérer la requête
    public function index(Request $request , UserPasswordHasherInterface $encoder): Response
    {

        $notification = null;
        // On appelle l'objet utilisateur en cours ($this) et on le récupére avec getUser()
        $user = $this->getUser();
        // On appelle dans les paramétres de createForm (Le formulaire'ChangePaswordType) et l'objet en cours.
        $form = $this->createForm(ChangePasswordType::class, $user);

        // Ecoute le formulaire pour savoir si on lui envoi une requête
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // On appelle le formulaire soumis et on récupere la données transmise dans le champs old_password
            $old_password = $form->get('old_password')->getData();
           
            // Méthode pour savoir si le password est valide (l'objet $user en cours et le mdp taper par l'utilisateur)
            if($encoder->isPasswordValid($user,$old_password)){
                //Ici le mot de passe en BDD et celui transmis sont pareil
                // On récupére le nouveau mot de passe 
                $new_password = $form->get('new_password')->getData();
                
                // on crypte le nouveau mdp
                $password = $encoder->hashPassword($user,$new_password);

                $user->setPassword($password);
                                
                //Méthode permettant d'enregistrer les donnée de persist() dans la base de données
                $this->entityManager->flush(); 
                $notification = "Votre mot de passe à bien été modifié.";
            }else{
                $notification = "Votre mot de passe actuel n'est pas le bon.";
            }

        }
        return $this->render('account/modifmdp.html.twig',['form' => $form->createView(),
                                                            'notification' => $notification]);
    }
}

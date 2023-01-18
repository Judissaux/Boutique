<?php

namespace App\Controller;

use App\Controller\Classes\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;




class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager= $entityManager;  
    }

    #[Route('/mot-de-passe-oublie', name: 'app_reset_password')]
    public function index(Request $request): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($request->get('email')){
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
            if($user){
                // Enregistrer en BDD la demande de reset password avec user,token et createdAt
                $reset_password= new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                //  Envoyer un email permettant de mofidier le mot de passe
                $url = $this->generateUrl('app_update_password', [
                    'token' => $reset_password->getToken()
                ]);
                $content = "Bonjour" . $user->getFullName() .",<br><br> Vous avez demandé à réinitialiser votre mot de passe. <br>";
                $content .= "Vous pouvez mettre à jour votre mot de passe en cliquant sur <a href='".$url."'> ce lien </a> <br><br>";
                $content .= "Bien cordialement toutes l'équipe de la Boutique Française";
                $mail = new Mail();
                $mail->send($user->getEmail(),$user->getFullName(),'Réinitialisation de mon mot de passe',$content);
                $this->addFlash('notice', "Vous allez recevoir d'ici quelques minutes un mail avec la procédure pour réinitialiser votre mot de passe");
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'app_update_password')]
    public function update(Request $request, $token,UserPasswordHasherInterface $encoder)
    {
        
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password){
            return $this->redirectToRoute('app_reset_password');
        }

        // Vérifier que le createdAt est = à maintenant -3h
       $date = new DateTimeImmutable();
       if($date > $reset_password->getCreatedAt()->modify('3 hours')){
           $this->addFlash('notice', 'Votre demande de réinitilisation de mot de passe a expiré. <br> Merci de la renouveller');
           return $this->redirectToRoute('app_reset_password');
       }
       
       // Création d 'une vue pour réinitialiser le mot de passe
       $form = $this->createForm(ResetPasswordType::class);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){

        $new_password = $form->get('new_password')->getData();
        
        // Encodage des mots de passe
        $password = $encoder->hashPassword($reset_password->getUser(),$new_password);
        $reset_password->getUser()->setPassword($password);

        // Flush en bdd
        $this->entityManager->flush();

        // Redirection de l'utilisateur vers la page de connexion
        $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');
        return $this->redirectToRoute('app_login');
       }

       return $this->render('reset_password/update.html.twig',[
        'form' =>$form->createView()
       ]);
       
       
      
    }

       
    
}


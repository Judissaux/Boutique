<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;





class OrderCrudController extends AbstractCrudController
{

    private $entityManager;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePrepation = Action::new('updatePreparation', 'Préparation en cours','fas fa-solid fa-cubes')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours','fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePrepation)
            ->add('detail', $updateDelivery)
            ->add('index','detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green'><strong>La commande " . $order->getreference() . " est bien <u> en cours de préparation</u></strong></span>");

        $url = $this->crudUrlGenerator
                ->setController(OrderCrudController::class)
                ->setAction('index')
                ->generateUrl();

        return $this->redirect($url);        
     
    }

    public function updateDelivery(AdminContext $context)
    {
        
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:orange'><strong>La commande " . $order->getreference() . " est bien <u> en cours de livraison</u></strong></span>");

        $url = $this->crudUrlGenerator
                ->setController(OrderCrudController::class)
                ->setAction('index')
                ->generateUrl();

        return $this->redirect($url);        
     
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' =>'desc']);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt','Date de création'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->formatValue(function ($value) { return $value; })->onlyOnDetail(),
            MoneyField::new('total','Total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice','Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non validée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails','Produits achetés')->hideOnIndex()
        ];
    }
    
}

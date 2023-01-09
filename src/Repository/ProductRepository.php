<?php

namespace App\Repository;

use App\Entity\Product;
use App\Controller\Classes\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Fonction qui permet de récupérer des données en fonction de la recherche de l'utilisateur
     * @return Product[]
     */
    public function findWithSearch(Search $search)
    {
        $query = $this
        //Permet de créer une requête SQL et a besoin en paramétre de la table visé ('p') équivaut à la table product
            ->createQueryBuilder('p')
        // Selection à l'intérieur de la requête ('c')pour catégory ('p') équivaut à la table product
            ->select('c','p')
        //Jointure entre les catégories du produit et la table catégory
            ->join('p.category','c');
        // Si j'ai des cases cochés dans le formulaire tu me récupére les catégorie
        if(!empty($search->categorie)){
            $query = $query
            //Rajoute les id des cqtegories dans la liste que l'on envoi en paramétes dans l'objet search
                ->andWhere('c.id IN (:categories)')
            // Permet de donner au paramétre :categorie  sa valeur c'est à dire la catégories récupérer dans l'objet search
                ->setParameter('categories', $search->categorie);
        }

        if(!empty($search->string)){
            $query =$query
            // Nom du produit es ce que sa ressemble à (LIKE) à ce que j'envoie via l'objet search
                ->andWhere('p.name LIKE :string')
            // le paramétre string passé plus haut (:string) et égal à la valeur présente dans le paramétre string de l'objet.
                ->setParameter('string', "%$search->string%");
        }
        // Permet d'exécuter la requéte et de récupérer les résultats
        return $query->getQuery()->getResult();
    }
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Type;
use App\Entity\Variant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Variant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Variant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Variant[]    findAll()
 * @method Variant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Variant::class);
    }

    // /**
    //  * @return Variant[] Returns an array of Variant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Variant
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function getListByCategorie($categorie)
    {
        if($categorie!=Type::CATEGORIE_CHAUSSANT && $categorie!=Type::CATEGORIE_TEXTILE){
            throw new Exception("Catégorie non gérée");
        }
        return $this->createQueryBuilder('v')
            ->addSelect('c, p,t,m')
            ->join('v.couleur', 'c')
            ->join('v.produit', 'p')
            ->join('p.type', 't')
            ->join('p.marque', 'm')
            ->andWhere('t.categorie = :cat_val')
            ->addOrderBy('t.nom')
            ->addOrderBy('p.nom')
            ->addOrderBy('c.nom')
            ->setParameter('cat_val', $categorie)
            ->getQuery()
            ->getResult()
            ;
    }
    public function getListTextile()
    {
        return $this->getListByCategorie(Type::CATEGORIE_TEXTILE);
    }
    public function getListChaussant()
    {
        return $this->getListByCategorie(Type::CATEGORIE_CHAUSSANT);
    }


    public function findOneById($variantId): ?Variant
    {
        return $this->createQueryBuilder('v')
            ->addSelect('c,p,m')
            ->join('v.couleur', 'c')
            ->join('v.produit', 'p')
            ->join('p.marque', 'm')
            ->andWhere('v.id = :id')
            ->setParameter('id', $variantId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}

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

    /**
     * Retrouve toutes les informations des variants d'une catégorie passée en argument.
     * La catégorie doit être connue sinon une exception est levée
     *
     * la requête charge les informations sur le variant, la couleur, le produit, le type et la marque.
     *
     * @param $categorie
     * @return array|null|
     * @throws Exception
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

    /**
     * Alias getListByCategorie($categorie) pour $categorie=T
     * @return array|null
     * @throws Exception
     */
    public function getListTextile()
    {
        return $this->getListByCategorie(Type::CATEGORIE_TEXTILE);
    }

    /**
     * Alias getListByCategorie($categorie) pour $categorie=C
     * @return array|null
     * @throws Exception
     */
    public function getListChaussant()
    {
        return $this->getListByCategorie(Type::CATEGORIE_CHAUSSANT);
    }

    /**
     * Retrouve 1 variant par son id
     * Les entités couleur, produit et marque sont également chargées
     * @param $variantId
     * @return Variant|null
     */
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

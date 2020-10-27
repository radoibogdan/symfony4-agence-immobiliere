<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    /**
     * @param Property[] $properties
     * Requête qui permet de récupérer l'ensemble des images
     * @return ArrayCollection
     */
    public function findForProperties(array $properties):ArrayCollection
    {
        $qb = $this->createQueryBuilder('p');
        $pictures = $qb
            ->select('p')                                                 // sélectionner toutes les informations qui ont attrait aux images
            ->where(
                $qb->expr()->in(
                    'p.id',                                                  // le champ qui va servir de condition, l'id de l'image
                     $this->createQueryBuilder('p2')
                        ->select('MAX(p2.id)')                        // MAX renvoie la valeur de l'id de l'image le plus grand parmi tous les id d'un bien
                        ->where('p2.property IN (:properties)')   // récupère toutes les images qui sont associées aux biens passés en paramètre
                        ->groupBy('p2.property')                   // grouper pour pouvoir récupérer qu'un seul, p.property = p.property_id
                        ->getDQL()
                )                                                           // => array de plusieurs picture.id
            )                                                               // => tous les champs d'une entité Picture avec l'id dans le array ci-dessous

            ->getQuery()
            ->setParameter('properties', $properties)
            ->getResult()
        ;
        // associer l'id du bien à l'image (key => value)
        $pictures = array_reduce($pictures, function (array $acc, Picture $picture) {
            $acc[$picture->getProperty()->getId()] = $picture;
            return $acc;
        }, []);
        return new ArrayCollection($pictures);
    }
    // /**
    //  * @return Picture[] Returns an array of Picture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Picture
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Property::class);
        $this->paginator = $paginator;
    }

    // ############## Récupère toutes les images (pagination dispo) sur la page Acheter ##############
    /**
     * @param PropertySearch $search
     * @param int $page
     * @return PaginationInterface
     */
    public function paginateAllVisible(PropertySearch $search, int $page)
    {
        $query = $this->findVisibleQuery(); // récupérer le QueryBuilder
        if ($search->getMaxPrice()) {
            $query = $query
                ->andWhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice())
            ;
        }
        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.surface >= :minsurface')
                ->setParameter('minsurface',$search->getMinSurface())
            ;
        }
        if ($search->getLat() && $search->getLng() && $search->getDistance()) {
            $query = $query
                ->andWhere('(6353 * 2 * ASIN(SQRT(POWER(SIN((p.lat - :lat) * pi()/180 / 2), 2) + COS(p.lat * pi()/180) * COS(:lat * pi()/180) * POWER(SIN((p.lng - :lng) * pi()/180 / 2), 2) ))) <= :distance')
                ->setParameter('lng', $search->getLng())
                ->setParameter('lat', $search->getLat())
                ->setParameter('distance', $search->getDistance())
            ;
        }
        if ($search->getOptions()->count() > 0) {
            $k = 0;
            foreach ($search->getOptions() as $option) {
                $k++;
                $query = $query
                    // p.options les options qui sont présentes dans le bien
                    // $options - options dans le filtre
                    ->andWhere(":option$k MEMBER OF p.options")
                    ->setParameter("option$k", $option)
                ;
            }
        }
        $properties = $this->paginator->paginate(
            $query->getQuery(),
            $page,
            12
        ); // renvoie un objet de type PaginatorInterface/SlidingPagination, getItems() pour avoir les objets Property
        $this->hydratePictures($properties);
        return $properties;
    }

    // ############## Récupère les derniers 4 biens ##############
    /**
     * @return Property[]
     */
    public function findLatest():array
    {
        $properties = $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ; // // renvoie un objet de type Array
        $this->hydratePictures($properties);
        return $properties;
    }

    // ############## utilisé par findLatest et findAllVisible ##############
    private function findVisibleQuery() : QueryBuilder
    {
        // createQueryBuilder => permet de concevoir une requête, p = tous les biens immobiliers
        return $this->createQueryBuilder('p')
            /*->select('p', 'pics') // les biens immo, les images associées
            ->leftJoin('p.pictures', 'pics')*/
            ->where('p.sold = false');
    }

    // ############## hydrater les biens avec les images ##############
    private function hydratePictures($properties) {
        if (method_exists($properties, 'getItems')) {
            $properties = $properties->getItems();
        }
        $pictures = $this->getEntityManager()->getRepository(Picture::class)->findForProperties($properties);
        foreach ($properties as $property) {
            /** @var Property $property */
            if ($pictures->containsKey($property->getId())) {
                $property->setPicture($pictures->get($property->getId()));
            }
        }
    }

}

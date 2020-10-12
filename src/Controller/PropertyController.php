<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class PropertyController extends AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/biens", name="property.index")
     * @return Response
     */
    public function index():Response
    {
        // $property = $this->repository->findAllVisible();
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }

    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug":"[a-z0-9\-]*"})
     * @param Property $property
     * @param string $slug
     * @return Response
     */
    public function show(Property $property, string $slug):Response
    {
        /*
            Le Slug transforme le titre Mon Bien immobillier => mon-bien-immobilier
            si la variable slug ($slug) récupérée depuis l'url
            ne correspond pas au string rétourné par getSlug du nom de la propriété
            => redirection vers cette même route avec le bon slug
            ex : /biens/mon-premier-moterroné-1 => /biens/mon-premier-bien-1
        */
        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show',[
               'id' =>$property->getId(),
               'slug'=>$property->getSlug()
            ], 301); // 301 car c'est une redirection permanente
        }
        // $property va récupérer tout seul l'id et va recherche l'entité Property avec l'id passé en argument
        return $this->render("property/show.html.twig",[
            'property' => $property,
            'current_menu' => 'properties'
        ]);
    }
}


/*
          $property = new Property();
           $property->setTitle('Mon premier bien')
               ->setPrice(200000)
               ->setRooms(3)
               ->setBedrooms(2)
               ->setDescription('Une petite descriptions')
               ->setSurface(40)
               ->setFloor(1)
               ->setHeat(1)
               ->setCity('Montpellier')
               ->setAddress('15 Boulevard Gambetta')
               ->setPostalCode('34000');
           $em = $this->getDoctrine()->getManager();
           $em->persist($property);
           $em->flush();
      */
<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{

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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);
        // après avoir mis les filtres et validé, le $search est automatiquement rempli grâce au système de formulaire

        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties', // utilisé dans le base.html.twig pour que le link soit highlighted
            'properties' => $this->repository->paginateAllVisible($search, $request->query->getInt('page',1)),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug":"[a-z0-9\-]*"})
     * @param Property $property
     * @param string $slug
     * @param Request $request
     * @param ContactNotification $notification
     * @return Response
     */
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
    {
        /*
        Le Slug transforme le titre Mon Bien immobillier => mon-bien-immobilier
        si la variable slug ($slug) récupérée depuis l'url
        ne correspond pas au string rétourné par getSlug du nom de la propriété
        => redirection vers cette même route avec le bon slug
        ex : /biens/mon-premier-moterroné-1 => /biens/mon-premier-bien-1
        */
        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301); // 301 car c'est une redirection permanente
        }

        // ###### Formulaire de contact créé ######/
        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // ###### Formulaire de contact envoyé ######/
        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé.');
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ]);
        }

        // $property va récupérer tout seul l'id et va recherche l'entité Property avec l'id passé en argument
        return $this->render("property/show.html.twig", [
            'property' => $property,
            'current_menu' => 'properties', // utilisé dans le base.html.twig pour que le link soit actif
            'form' => $form->createView()
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
<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Restaurant;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/avis", name: "app_avis_")]
class AvisController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private AvisRepository $repository
    ) {
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route("/", name: "index", methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('avis/index.html.twig', [
            'avis' => $this->getUser()->getAvis()
        ]);
    }

    public function avis(): Response
    {
        return $this->render('avis/index.html.twig', [
            'controller_name' => 'AvisController',
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route("/create/{id}", name: "create", methods: ['POST'])]
    public function create(Restaurant $restaurant, Request $request): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUser($this->getUser())
                ->setRestaurant($restaurant);

            $this->em->persist($avis);
            $this->em->flush();

            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
        }

        return null;
    }

    public function avisForm(Restaurant $restaurant): Response
    {

        $form = $this->createForm(AvisType::class);

        return $this->render('avis/_form.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant
        ]);
    }

    public function showByRestaurant(Restaurant $restaurant): Response
    {
        $avis = $restaurant->getAvis();

        return $this->render('avis/show_by_restaurant.html.twig', [
            'avis' => $avis,
            'nbAvis' => count($avis)
        ]);
    }

    #[Route("/{id}", name: "show", methods: ['GET'])]
    public function show(Avis $avis): Response
    {
        return $this->render("avis/show.html.twig", [
            "avis" => $avis
        ]);
    }
}

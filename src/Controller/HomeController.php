<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{

    #[Route("/", name: "app_home", methods: ["GET"])]
    #[Cache(smaxage: 10)]
    public function home(RestaurantRepository $repository, EntityManagerInterface $em): Response
    {
        return $this->render("home/home.html.twig", [
            "restaurants" => $repository->findBy([], ["createdAt" => "desc"], 4),
            "bestRestaurants" => $repository->findBestRestaurant(4)
        ]);
    }

    #[Route("/mentions-legales", name: "app_mentions_legales", methods: ["GET"])]
    public function mentions(): Response
    {
        return $this->render("home/mentions.html.twig");
    }
}

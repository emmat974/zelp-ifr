<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class HomeController extends AbstractController
{

    #[Route("/", name: "app_home", methods: ["GET"])]
    #[Cache(smaxage: 10)]
    public function home(RestaurantRepository $repository, EntityManagerInterface $em): Response
    {
        // $bestRestaurant = $cache->get('best_restaurants', function (ItemInterface $item) use ($em) {
        //     $item->expiresAfter(3600);
        $query = $em
            ->getRepository(Restaurant::class)
            ->createQueryBuilder('r')
            ->select('r')
            ->leftJoin('r.avis', 'a')
            ->groupBy('r.id')
            ->orderBy('AVG(a.rating)', 'DESC')
            ->setMaxResults(4) // Limiter le nombre de résultats à 4
            ->getQuery();

        $bestRestaurant =  $query->getResult();
        // });

        return $this->render("home/home.html.twig", [
            "restaurants" => $repository->findBy([], ["createdAt" => "desc"], 4),
            "bestRestaurants" => $bestRestaurant
        ]);
    }

    #[Route("/mentions-legales", name: "app_mentions_legales", methods: ["GET"])]
    public function mentions(): Response
    {
        return $this->render("home/mentions.html.twig");
    }
}

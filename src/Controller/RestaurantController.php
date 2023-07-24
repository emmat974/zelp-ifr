<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Restaurant;
use App\Entity\RestaurantPicture;
use App\Form\AvisType;
use App\Form\RestaurantType;
use App\Repository\AvisRepository;
use App\Repository\RestaurantRepository;
use App\Service\Upload;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{

    public function __construct(private Upload $upload)
    {
    }

    #[Route('/', name: 'app_restaurant_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->getRestaurantSearchQuery($em, $request);

        $paginator = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $paginator,
        ]);
    }

    private function getRestaurantSearchQuery(EntityManagerInterface $em, Request $request)
    {
        $searchTerm = $request->query->get('search');
        $qb = $em->createQueryBuilder();

        $qb->select('r')
            ->from(Restaurant::class, 'r')
            ->orderBy('r.createdAt', 'DESC');

        if ($searchTerm) {
            $qb->andWhere($qb->expr()->like('r.name', ':searchTerm'))
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $qb;
    }

    #[IsGranted("ROLE_RESTAURATEUR")]
    #[Route("/dashboard", name: 'app_restaurant_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('restaurant/dashboard.html.twig', [
            'restaurants' => $this->getUser()->getRestaurants()
        ]);
    }

    #[IsGranted("ROLE_RESTAURATEUR")]
    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $restaurantPicture = new RestaurantPicture();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant->setUser($this->getUser());
            // ->addRestaurantPicture($restaurantPicture);
            $entityManager->persist($restaurant);

            // $file = $form->get('images')->getData();

            // if ($file) {
            //     $this->uploadFile($restaurant, $file, $entityManager);
            // }
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[IsGranted("ROLE_RESTAURATEUR")]
    #[Route('/{id}/edit', name: 'app_restaurant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($restaurant->getUser() != $this->getUser()) {
            $this->redirectToRoute("app_home");
        }
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[IsGranted("ROLE_RESTAURATEUR")]
    #[Route('/{id}', name: 'app_restaurant_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($restaurant->getUser() != $this->getUser()) {
            $this->redirectToRoute("app_home");
        }

        if ($this->isCsrfTokenValid('delete' . $restaurant->getId(), $request->request->get('_token'))) {
            foreach ($restaurant->getRestaurantPictures() as $pictures) {
                $this->upload->remove($pictures->getFile());
                $entityManager->remove($pictures);
            }
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_restaurant_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    private function uploadFile(Restaurant $restaurant, $file, EntityManagerInterface $entityManager)
    {
        $fileName = $this->upload->upload($file);

        $restaurantPicture = new RestaurantPicture;
        $restaurantPicture
            ->setName($fileName)
            ->setFile($fileName)
            ->setRestaurant($restaurant);

        $entityManager->persist($restaurantPicture);
    }
}

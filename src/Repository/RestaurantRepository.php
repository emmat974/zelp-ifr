<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 *
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function findBestRestaurant(int $limit = 4): array
    {
        return $this
            ->createQueryBuilder('r')
            ->select('r')
            ->leftJoin('r.avis', 'a')
            ->groupBy('r.id')
            ->orderBy('AVG(a.rating)', 'DESC')
            ->setMaxResults(4) // Limiter le nombre de résultats à 4
            ->getQuery()
            ->getResult();
    }

    public function searchRestaurant(string $search = null)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r')
            ->orderBy('r.createdAt', 'DESC');

        if ($search) {
            $qb->andWhere($qb->expr()->like('r.name', ':searchTerm'))
                ->setParameter('searchTerm', '%' . $search . '%');
        }

        return $qb;
    }

    //    /**
    //     * @return Restaurant[] Returns an array of Restaurant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restaurant
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

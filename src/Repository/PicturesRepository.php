<?php

namespace App\Repository;

use App\Entity\Pictures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pictures|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pictures|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pictures[]    findAll()
 * @method Pictures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PicturesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pictures::class);
    }

    /**
     * @return Pictures[] Returns an array of Pictures objects
     */
    public function lastThree()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.dateUpload', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("Route", name="RouteName")
     */
    public function findAllPictures($category): array
    {
        return $this->createQueryBuilder('p')
            ->where(':category MEMBER OF p.category')
            ->setParameter('category', $category)
            ->orderBy('p.dateUpload', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

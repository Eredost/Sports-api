<?php

namespace App\Repository;

use App\Entity\Sport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sport::class);
    }

    public function getSports(int $offset, int $limit, string $order, string $term = null)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s')
            ->orderBy('s.label', $order)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        if ($term) {
            $queryBuilder->andWhere('s.label LIKE ?1')
                ->setParameter(1, '%' . $term . '%')
            ;
        }

        return $queryBuilder->getQuery()
            ->getResult()
        ;
    }
}

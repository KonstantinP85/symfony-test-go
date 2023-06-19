<?php

namespace App\Repository;

use App\Entity\Post;
use App\Enum\PostStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param Criteria $criteria
     * @return array|Post[]
     */
    public function searchPostList(Criteria $criteria): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb->addCriteria($criteria);

        return $qb->getQuery()->getResult();
    }
}
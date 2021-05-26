<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }





    public function getUserFeed(array $followingIds): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select("p")
            ->from(Post::class, "p")
            ->leftJoin(
                User::class,
                "u",
                Join::WITH,
                "p.user = u.id"
            )
            ->where("u.id IN (:ids)")
            ->andWhere("p.isPublished = :published")
            ->setParameter("published", true)
            ->setParameter("ids", $followingIds, Connection::PARAM_INT_ARRAY)
            ->orderBy("p.createdDate", "DESC")
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function countAdmins(): int
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('COUNT(u.id)');
        $qb->andWhere($qb->expr()->like('u.roles', ':role'));
        $qb->setParameter('role', '%"ROLE_ADMIN"%');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    //    /**
    //     * @return Orders[] Returns an array of Orders objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Orders
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findActiveCartByClient(?\Symfony\Component\Security\Core\User\UserInterface $user): ?Orders
    {
        if (!$user) {
            return null;
        }

        return $this->createQueryBuilder('o')
            ->andWhere('o.client = :user')
            ->andWhere('o.is_paid = false')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCurrentCart(Clients $user): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->where('o.client = :user')
            ->andWhere('o.is_paid = :isPaid')
            ->setParameters([
                'user' => $user,
                'isPaid' => false
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCartWithLock(Clients $client): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->where('o.client = :client')
            ->andWhere('o.is_paid = :isPaid')
            ->setParameters([
                'client' => $client,
                'isPaid' => false
            ])
            ->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)
            ->getQuery()
            ->getOneOrNullResult();
    }

}

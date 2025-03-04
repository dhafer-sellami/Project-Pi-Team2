<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findByUserAndDateRange(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.utilisateur = :user')
            ->andWhere('n.dateCreation BETWEEN :start_date AND :end_date')
            ->setParameter('user', $user)
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->orderBy('n.dateCreation', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findUnreadByUser(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.utilisateur = :user')
            ->andWhere('n.estLue = :estLue')
            ->setParameter('user', $user)
            ->setParameter('estLue', false)
            ->orderBy('n.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\PriseMedicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriseMedicament>
 *
 * @method PriseMedicament|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriseMedicament|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriseMedicament[]    findAll()
 * @method PriseMedicament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriseMedicamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriseMedicament::class);
    }

    public function save(PriseMedicament $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PriseMedicament $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function trouverPrisesAVenir(\DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateHeurePrise BETWEEN :debut AND :fin')
            ->andWhere('p.pris = false')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingMedications(\DateTime $now): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.dateHeurePrise > :now')
            ->andWhere('p.dateHeurePrise <= :reminder_time')
            ->andWhere('p.pris = :pris')
            ->setParameter('now', $now)
            ->setParameter('reminder_time', (clone $now)->modify('+30 minutes'))
            ->setParameter('pris', false)
            ->orderBy('p.dateHeurePrise', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findByPatientAndDateRange(User $patient, \DateTime $startDate, \DateTime $endDate): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.patient = :patient')
            ->andWhere('p.dateHeurePrise BETWEEN :start_date AND :end_date')
            ->setParameter('patient', $patient)
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->orderBy('p.dateHeurePrise', 'DESC');

        return $qb->getQuery()->getResult();
    }
}

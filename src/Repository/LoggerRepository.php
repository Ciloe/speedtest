<?php

namespace App\Repository;

use App\Entity\Logger;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Logger|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logger|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logger[]    findAll()
 * @method Logger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoggerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logger::class);
    }

    public function findLatest(DateTimeImmutable $startedAt = null, DateTimeImmutable $endedAt = null): array
    {
        $query = $this->createQueryBuilder('l')
            ->andWhere('l.launchedAt > :started_at')
            ->setParameter('started_at', $startedAt ?? (new DateTimeImmutable())->modify('- 1 day'))
            ->orderBy('l.launchedAt', 'ASC');

        if (!is_null($endedAt)) {
            $query->andWhere('l.launchedAt < :ended_at')
                ->setParameter('ended_at', $endedAt);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function getAvgUpload(): float
    {
        return $this->createQueryBuilder('l')
            ->select('AVG(l.upload)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAvgDownload(): float
    {
        return $this->createQueryBuilder('l')
            ->select('AVG(l.download)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAvgLatency(): float
    {
        return $this->createQueryBuilder('l')
            ->select('AVG(l.latency)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

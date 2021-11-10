<?php

namespace App\Repository;

use App\Entity\Logger;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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

    public function findLatest(
        ?DateTimeImmutable $startedAt = null,
        ?DateTimeImmutable $endedAt = null,
        ?string $sponsor = null,
        ?string $city = null,
    ): array {
        $sql = <<<SQL
SELECT l.* 
FROM logger l
WHERE l.launched_at > :started_at
    AND CASE WHEN (:ended_at)::timestamp IS NOT NULL THEN l.launched_at < :ended_at ELSE true END 
    AND CASE WHEN (:sponsor)::TEXT IS NOT NULL THEN l.server::TEXT LIKE CONCAT('%"sponsor":"', (:sponsor)::TEXT, '"%') ELSE true END 
    AND CASE WHEN (:city)::TEXT IS NOT NULL THEN l.server::TEXT LIKE CONCAT('%"name":"', (:city)::TEXT, '"%') ELSE true END 
ORDER BY l.launched_at
SQL;

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Logger::class, 'l');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query
            ->setParameter('started_at', $startedAt ?? (new DateTimeImmutable())->modify('- 1 day'))
            ->setParameter('ended_at', $endedAt)
            ->setParameter('sponsor', $sponsor)
            ->setParameter('city', $city)
        ;
        return $query->getResult(AbstractQuery::HYDRATE_SIMPLEOBJECT);
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

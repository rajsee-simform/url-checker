<?php

namespace App\Repository;

use App\Entity\Urls;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Urls>
 *
 * @method Urls|null find($id, $lockMode = null, $lockVersion = null)
 * @method Urls|null findOneBy(array $criteria, array $orderBy = null)
 * @method Urls[]    findAll()
 * @method Urls[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Urls::class);
    }

    public function insert($values): int
    {
        $conn = $this->getEntityManager()->getConnection();
        //This will avoid duplicate insertions
        $sql = "INSERT IGNORE INTO urls (hash, url) values $values";
        $stmt = $conn->prepare($sql);

        return $stmt->executeStatement();

    }
}

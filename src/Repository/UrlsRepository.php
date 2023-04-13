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

    /**
     * @param $values ("hash", "url), ("hash","url), .....
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert($values): int
    {
        $conn = $this->getEntityManager()->getConnection();

        //This approach executes the query in batch of records, choose batch size according to your server and mysql configurations
        $affectedRows = 0;
        $values = array_chunk($values, 5000);

        foreach ($values as $value) {

            $valuesTobeInserted = implode(",", $value);
            //This will avoid duplicate insertions
            $sql = "INSERT IGNORE INTO urls (hash, url) values $valuesTobeInserted";
            $stmt = $conn->prepare($sql);

            $affectedRows += $stmt->executeStatement();

        }
        return $affectedRows;

        /*This approach executes only one query and insert all the records, but it has limitation of Mysql packet size so choose this spproach if you
        know all limitations*/

        //This will avoid duplicate insertions
       /* $sql = "INSERT IGNORE INTO urls (hash, url) values $values";
        $stmt = $conn->prepare($sql);

        //It will return total affected rows
        return $stmt->executeStatement();*/


        //This approach executes the single query for each row, it was taking 17000ms to insert 13k records
        /*array_map(function ($value) use (&$conn, &$affectedRows){

            //This will avoid duplicate insertions
            $sql = "INSERT IGNORE INTO urls (hash, url) values $value";
            $stmt = $conn->prepare($sql);

            $affectedRows += $stmt->executeStatement();

        }, $values);

        return $affectedRows;*/


    }
}

<?php

namespace App\Service;

use App\Entity\Urls;
use Doctrine\ORM\EntityManagerInterface;

class URLService
{
    private $entityManager;
    private $normalizedUrls;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->normalizedUrls = [];
    }

    const HASH_ALGORITHM = 'sha256';

    public function checkUrlsExist($urls): array
    {
        $isDuplicate = true;
        $flush = array_map(array($this, 'normalizeUrl'), $urls);

        $countOfDataToBeInserted = count($this->normalizedUrls);

        if($countOfDataToBeInserted > 0) {

            //$valuesToBeInserted = implode(",", array_filter($flush, 'strlen'));
            $valuesToBeInserted =array_filter($flush, 'strlen');
            //Insert data
            $affectedRows = $this->entityManager->getRepository(Urls::class)->insert($valuesToBeInserted);
            $duplicateRecords = $countOfDataToBeInserted - $affectedRows;
            $isDuplicate = ($duplicateRecords > 0) ? true : false;
        }

        return [
            'isDuplicate' => $isDuplicate,
            'insertedRecords' => $affectedRows,
            'duplicateRecords' => $duplicateRecords
        ];
    }

    /**
     * This function will calculate hash after normalizing URL
     * @param string $url
     * @return false|string
     */
    private function normalizeUrl(string $url)
    {
        if(!empty($url)){

            $urlParts = parse_url($url);

            $scheme = (!empty($urlParts['scheme'])) ? strtolower($urlParts['scheme']) : null;
            $host = (!empty($urlParts['host'])) ? strtolower($urlParts['host']) : null;
            $port = ($urlParts['port']) ?? null;
            $path = ($urlParts['path']) ?? null;
            $query = ($urlParts['query']) ?? null;
            $fragment = ($urlParts['fragment']) ?? null;

            if ($port && (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443))) {
                $host .= ':' . $port;
            }

            $queryParams = array();

            //Sorting the param to make uniform comparison
            if ($query) {
                parse_str($query, $queryParams);
                ksort($queryParams);
                $query = http_build_query($queryParams);
            }

            //To identify whether the two URLs are identical, will convert all URLs like this
            $normalizedURL = $host . $path . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');

            //Calculate hash value for each normalized URL
            $hash = hash(self::HASH_ALGORITHM, $normalizedURL);

            //If duplicates found in the file itself, then it will be ignored
            if( !isset($this->normalizedUrls[$hash] )){

                $this->normalizedUrls[$hash] = $url;
                //To batch insert the data, prepare it like ("hash", "value")
                return "('$hash', '$url')";
            }
        }

        return false;
    }
}

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

    public function checkUrlsExist($urls): bool
    {
        $isDuplicate = true;
        $flush = array_map(array($this, 'normalizeUrl'), $urls);

        $countOfDataToBeInserted = count($this->normalizedUrls);

        if($countOfDataToBeInserted > 0) {

            //Comma separated values in a batch
            $valuesToBeInserted = implode(",", array_filter($flush, 'strlen'));
            $affectedRows = $this->entityManager->getRepository(Urls::class)->insert($valuesToBeInserted);
            $isDuplicate = (($countOfDataToBeInserted - $affectedRows) > 0) ? true : false;
        }

        return $isDuplicate;
    }

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

            if ($query) {
                parse_str($query, $queryParams);
                ksort($queryParams);
                $query = http_build_query($queryParams);
            }

            $normalizedURL = $host . $path . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');

            $hash = hash(self::HASH_ALGORITHM, $normalizedURL);

            if( !isset($this->normalizedUrls[$hash] )){
                $this->normalizedUrls[$hash] = $url;
                return "('$hash', '$url')";
            }
        }

        return false;
    }
}

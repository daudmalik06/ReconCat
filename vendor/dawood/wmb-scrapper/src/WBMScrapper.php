<?php

namespace dawood\WBMScrapper;

class WBMScrapper
{

    /**
     * fetch all possible snapshot urls from archive.org
     * @param string $url
     * @return array
     */
    public static function getAllSnapShotUrls($url)
    {
        $firstYear = self::firstSnapshotYear($url);
        $lastYear = self::lastSnapshotYear($url);

        $allSnapShotUrls = [];
        for($year = $firstYear; $year <= $lastYear; $year++)
        {
            $allSnapShotUrls = array_unique(array_merge($allSnapShotUrls , self::getSnapShotUrlsOfYear($url, $year)));
        }
        return $allSnapShotUrls;
    }


    /**
     * fetch snapshot url of provided $year from archive.org
     * @param string $url
     * @param int $year
     * @return array
     */
    public static function getSnapShotUrlsOfYear($url, $year)
    {
        $scrapper = new self;
        $snapShotAddress = 'https://web.archive.org/__wb/calendarcaptures?url='.urlencode($url).'&selected_year='.$year;
        $response = file_get_contents($snapShotAddress);
        preg_match_all('/"ts":\[(.*?)\]/',$response, $timestamps);
        $finalTimestamps = [];
        foreach ($timestamps[1] as $timestamp)
        {
            if(strstr($timestamp, ','))
            {
                //it's array
                foreach (explode(',', $timestamp) as $subTimestamp)
                {
                    $finalTimestamps[] = $subTimestamp;
                }
                continue;
            }
            $finalTimestamps[] = $timestamp;
        }
        $finalTimestamps = array_unique($finalTimestamps, SORT_NUMERIC );
        return $scrapper->prepareWebArchiveUrlsFromArray($url, $finalTimestamps);
    }

    /**
     * returns first year of snapshot taken of provided $url
     * @param string $url
     * @return int
     */
    public static function firstSnapshotYear($url)
    {
        $scrapper = new self;
        return (int)$scrapper->getYears($url)['first'];
    }

    /**
     * returns last year of snapshot taken of provided $url
     * @param $url
     * @return int
     */
    public static function lastSnapshotYear($url)
    {
        $scrapper = new self;
        return (int)$scrapper->getYears($url)['last'];
    }

    /**
     * @param string $url
     * @return array
     */
    private function getYears($url)
    {
        $infoAddress = 'https://web.archive.org/__wb/sparkline?url='.urlencode($url).'&collection=web&output=json';
        $jsonResponse = file_get_contents($infoAddress);
        $jsonResponse= json_decode($jsonResponse, true);
        return [
            'first' => substr($jsonResponse['first_ts'], 0 ,4),
            'last' => substr($jsonResponse['last_ts'], 0 ,4),
        ];
    }

    /**
     * @param string $url
     * @param array $timestamps
     * @return array
     */
    private function prepareWebArchiveUrlsFromArray($url, array $timestamps)
    {
        $webArchiveUrls = [];
        $webArchiveAddress = 'https://web.archive.org/web/TIME_STAMP/'.$url;
        foreach ($timestamps as $timestamp) {
            $webArchiveUrls[] = str_replace('TIME_STAMP',$timestamp, $webArchiveAddress);
        }
        return $webArchiveUrls;
    }

}
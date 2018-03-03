<?php
/**
 * Created by PhpStorm.
 * User: daudm
 * Date: 2/25/2018
 * Time: 9:46 PM
 */

namespace dawood\ReconCat;

use dawood\WBMScrapper\WBMScrapper;
use Threaded;

class WBMScrapperClient extends Threaded
{
    /**
     * @var int
     */
    private $year;
    /**
     * @var bool
     */
    private $verbose;
    /**
     * @var string
     */
    private $url;
    private $finishedWorking;
    private $snapshots;

    /**
     * WBMScrapperClient constructor.
     * @param int $year
     * @param string $url
     * @param bool $verbosity
     * @param $callback
     * @internal param $output
     */
    public function __construct($year, $url, $verbosity = false)
    {
        $this->year = $year;
        $this->verbose = $verbosity;
        $this->url = $url;
    }

    public function run()
    {
        $ds = DIRECTORY_SEPARATOR;
        $snapshots = WBMScrapper::getSnapShotUrlsOfYear($this->url, $this->year);
        $url = parse_url($this->url)['host'];
        $outputDir = rootDirectory().$ds."Output".$ds.$url;
        if(!file_exists($outputDir))
        {
            mkdir($outputDir);
        }
        $file = $outputDir.$ds.$this->year.'_'.$url.'.txt';
        file_put_contents($file,'');
        foreach ($snapshots as $snapshot)
        {
            file_put_contents($file,$snapshot.PHP_EOL, FILE_APPEND);
        }
        if($this->verbose)
        {
            print_r($snapshots);
        }
        $this->snapshots = $snapshots;
        echo $this->year.' Fetched'.PHP_EOL;
        $this->finishedWorking = true;
    }

    public function isDone()
    {
        return $this->finishedWorking;
    }

    public function getData()
    {
        return $this->snapshots;
    }
}
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
    public $year;
    /**
     * @var string
     */
    private $url;
    private $data;
    private $finishedWorking;

    /**
     * WBMScrapperClient constructor.
     * @param int $year
     * @param string $url
     * @param $callback
     * @internal param $output
     */
    public function __construct($year, $url)
    {
        $this->year = $year;
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
        $this->data=file_get_contents($file);
        $this->finishedWorking = true;
    }

    public function isDone()
    {
        return $this->finishedWorking;
    }

    public function getData()
    {
        return $this->data;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: daudm
 * Date: 2/25/2018
 * Time: 4:38 PM
 */


use dawood\WBMScrapper\WBMScrapper;
include "vendor/autoload.php";

$url = 'https://github.com/';
$aliExpressFirstSnapShotYear = WBMScrapper::firstSnapshotYear($url);
$aliExpressLastSnapShotYear = WBMScrapper::lastSnapshotYear($url);
echo $aliExpressFirstSnapShotYear.PHP_EOL;
echo $aliExpressLastSnapShotYear.PHP_EOL;

$snapshotsOf2012 = WBMScrapper::getSnapShotUrlsOfYear($url, 2012);
print_r(snapshotsOf2012 );

$allSnapshots = WBMScrapper::getAllSnapShotUrls($url);
print_r($allSnapshots);
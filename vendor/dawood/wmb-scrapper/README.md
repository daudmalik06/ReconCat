## WMB Scrapper


=========================================  

[![Latest Stable Version](https://poser.pugx.org/dawood/wmb-scrapper/v/stable.svg)](https://packagist.org/packages/dawood/wmb-scrapper)
[![Total Downloads](https://poser.pugx.org/dawood/wmb-scrapper/downloads)](https://packagist.org/packages/dawood/wmb-scrapper)
[![License](https://poser.pugx.org/dawood/wmb-scrapper/license.svg)](https://packagist.org/packages/dawood/wmb-scrapper)

## Introduction
A small Php package to fetch archive url snapshots from archive.org.  
using it you can fetch complete list of snapshot urls of any year or 
complete list of all years possible.  
**This package can be used to do recon of any target.**



## Installation

Install the package through [composer](http://getcomposer.org):

```
composer require dawood/wmb-scrapper
```

Make sure, that you include the composer [autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading)
somewhere in your codebase.

## Examples

There are several examples provided in examples folder too.  

### Get first/last snapshot year of domain
    include "vendor/autoload.php";
    use dawood\WBMScrapper\WBMScrapper;
    
    $url = 'https://github.com/';
    $firstSnapShotYear = WBMScrapper::firstSnapshotYear($url);
    $lastSnapShotYear = WBMScrapper::lastSnapshotYear($url);
    echo $lastSnapShotYear .PHP_EOL;
    echo $firstSnapShotYear.PHP_EOL;
    
    
### Get snapshots of any year of domain
    include "vendor/autoload.php";
    use dawood\WBMScrapper\WBMScrapper;
    
    $url = 'https://github.com/';
    $snapshotsOf2012 = WBMScrapper::getSnapShotUrlsOfYear($url, 2012);
    print_r(snapshotsOf2012 );
    //outputs list of urls of waybackmachin snapshots
    e.g
    https://web.archive.org/web/20091226225818/http://www.github.com/
        
### Get snapshots of all years of domain
    include "vendor/autoload.php";
    use dawood\WBMScrapper\WBMScrapper;
    
    $url = 'https://github.com/';
    $allSnapshots = WBMScrapper::getAllSnapShotUrls($url);
    print_r($allSnapshots);
    
    //outputs a complete list of urls of waybackmachin snapshots
    e.g
    https://web.archive.org/web/20091226225818/http://www.github.com/
    
    
## License
The **WMB Scrapper** is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contribution
Thanks to all of the contributors ,  

## Author
Dawood Ikhlaq and Open source community
    


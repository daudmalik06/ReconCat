## Recon Cat


=========================================  


## Introduction
A small Php application to fetch archive url snapshots from archive.org.  
using it you can fetch complete list of snapshot urls of any year or 
complete list of all years possible.  
Made Specially for penetration testing purpose.  
**This application is powered by [WMB-Scrapper](https://github.com/daudmalik06/WMB-Scrapper)**



## Installation

Clone this repository,

```
    git clone https://github.com/daudmalik06/ReconCat
    cd ReconCat
    php recon
```

## Requirements

- This application requires php 7+  
- multi threading is available as optional, if you have php [pthreads](https://github.com/krakjoe/pthreads) installed you can use that
to speed up the process.


## Information

- it saves all snapshots in Output directory, e,g for google.com it will
make a directory as `Output/google.com` and will save all related snapshot in that directory  
- all snapshot will be saved on year bases, i.e snapshot of every year will be saved in different file
 e.g 2009_google.com .
- threads are used for fetching several(year based) snapshot concurrently 
- single year snapshot is fetched in a single thread 
 
 ## Usage
 
For help  


```
php recon --help
```

![ReconCat Example](/src/reconCatExample.JPG)
![ReconCat Help](/src/reconCatHelp2.JPG)

Other commands
```
php recon --url=https://github.com -t10  (fetch all snapshot of github with 10 threads)
php recon -y2012 --url=https://github.com -t10  (fetch snapshot of year 2012 of github with 10 threads)
```

## License
The **Recon Cat** is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contribution
Thanks to all of the contributors ,  

## Author
Dawood Ikhlaq and Open source community
    

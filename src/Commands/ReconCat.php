<?php

namespace dawood\ReconCat\Commands;

use dawood\ReconCat\Console;
use dawood\WBMScrapper\WBMScrapper;
use dawood\ReconCat\WBMScrapperClient;
use Pool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReconCat extends Command
{
    protected function configure()
    {
        $this
            ->setName('recon')
            ->setDescription('gather the endpoints given url from way back machine.')
            ->setHelp('Example:'.PHP_EOL.'recon --url=google.com --year=2009'.PHP_EOL.'to gather all snapshots'.PHP_EOL.'recon --url=desiredUrl --year=all')
            ->addOption('url','u',  InputOption::VALUE_REQUIRED, 'The Url to gather the snapshot from wayBackMachine')
            ->addOption('year','y',  InputOption::VALUE_REQUIRED, 'The Year of snapshots from wayBackMachine, acceptable options are (year, all)', "all")
            ->addOption('threads','t',  InputOption::VALUE_OPTIONAL, 'Threads to be used', 5)
            ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');
        $year = $input->getOption('year');
        $this->printHelpIfArgumentMissing($input);

        $output->writeln('<info>Fetching All Archive Links</info>');
        $output->writeln('<comment>Using Threads:'.$input->getOption('threads').'</comment>');


        if($year == 'all')
        {
            $this->processAllSnapshots($url, $output, $input);
        }else{
            (new WBMScrapperClient($year, $url, $input->getOption('verbose')))->run();
        }
    }

    /**
     * @param InputInterface $input
     */
    private function printHelpIfArgumentMissing(InputInterface $input)
    {
        $url = $input->getOption('url');
        if(empty($url))
        {
            $command = $this->getApplication();

            $arguments = array(
                'command' => 'recon',
                '--help' => true,
            );
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput);
            return ;
        }
    }

    /**
     * @param $url
     * @param OutputInterface $output
     * @param InputInterface $input
     */
    private function processAllSnapshots($url, OutputInterface $output, InputInterface $input)
    {
        $threads = (int)$input->getOption('threads');
        $verbose = $input->getOption('verbose');
        $firstYear = WBMScrapper::firstSnapshotYear($url);
        $lastYear = WBMScrapper::lastSnapshotYear($url);
        $pool = new Pool($threads);
        for($year = $firstYear; $year <= $lastYear; $year++)
        {
            $pool->submit(new WBMScrapperClient($year, $url, $verbose));
        }
        while($pool->collect());
        $pool->shutdown();
        echo 'all done'.PHP_EOL;
    }

}
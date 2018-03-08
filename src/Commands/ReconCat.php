<?php

namespace dawood\ReconCat\Commands;

use dawood\WBMScrapper\WBMScrapper;
use dawood\ReconCat\WBMScrapperClient;
use Pool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
        return;
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
        $progressBar = $this->getProgressBar($output , (int)($lastYear-$firstYear));

        $collector = function (WBMScrapperClient $task) use($progressBar, $verbose, $output){
            if($task->isDone()) {
                $year = $task->year;
                $links = explode(PHP_EOL, $task->getData());
                if ($verbose) {
                    $output->writeln('<info></info>');
                    foreach ($links as $link) {
                        if (empty($link)) {
                            continue;
                        }
                        $output->writeln('<info>' . $link . '</info>');
                    }
                }
                $progressBar->setMessage('<fg=white;options=bold>'.$year.'</>', 'year');
                $progressBar->advance();
                return true;
            }
            return false;
        };
        $pool = new Pool($threads);
        for($year = $firstYear; $year <= $lastYear; $year++)
        {
            $pool->submit(new WBMScrapperClient($year, $url, $verbose));
        }
        while($pool->collect($collector)){
            usleep(100);
        }
        $progressBar->finish();
        return;
    }

    private function getProgressBar(OutputInterface $output,int $total)
    {
        $progressBar = new ProgressBar($output, $total);

        $progressBar->setBarCharacter('<fg=green>=</>');
        $progressBar->setEmptyBarCharacter("<fg=red>-</>");
        $progressBar->setProgressCharacter("<fg=green;options=bold>></>");
        $progressBar->setBarWidth(50);

        $progressBar->setFormat(
            "Year Fetched:%year%\n%current%/%max%[%bar%]"
        );
        $progressBar->start();
        return $progressBar;
    }

}
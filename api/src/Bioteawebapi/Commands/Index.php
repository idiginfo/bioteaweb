<?php

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Index extends Command
{
    const NO_LIMIT = 0;

    protected function configure()
    {
        $this->setName('index')->setDescription('Index documents into MySQL (and SOLR, if app is configuerd to do so)');
        $this->addArgument('path', InputArgument::REQUIRED, 'Folder path to index');
        $this->addOption('quiet', 'q', InputOption::VALUE_NONE, 'Quiet mode');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit number of indexed documents (excluding skipped)');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int) $input->getOption('limit') ?: self::NO_LIMIT;
        $path  = $input->getArgument('path');

        if (is_readable($path)) {
            $path = realpath($path);
        }
        else {
            throw new \Exception("Path is not readable:" . $path);
        }

        //Build TaskTracker object
            //if no -q, set output to Symfony output object
            //if -l set, use a limited TaskTracker

        //Send the taskTracker object to the indexer

        //Run the indexer...
        $this->app['indexer']->index($path, $limit);
    }
}
/* EOF: Index.php */
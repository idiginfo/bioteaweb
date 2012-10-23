<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\Tracker;
use TaskTracker\OutputHandler\SymfonyConsole as TrackerConsoleHandler;
use TaskTracker\OutputHandler\Monolog as TrackerMonologHandler;

/**
 * Recursively index RDF file and report output to logs and to the console
 */
class Index extends Command
{
    const NO_LIMIT = 0;

    protected function configure()
    {
        $this->setName('index')->setDescription('Index documents into MySQL (and SOLR, if app is configuerd to do so)');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Folder path to index. If not specified, path in config is used');
        $this->addOption('quiet', 'q', InputOption::VALUE_NONE, 'Quiet mode');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit number of indexed documents (excluding skipped)');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int) $input->getOption('limit') ?: self::NO_LIMIT;

        //Get the path if it is specified
        if ($input->getArgument('path')) {
            $this->app['fileclient']->setBasePath($input->getArgument('path'));
        }

        //Output to log
        $trackerHandlers = array(new TrackerMonologHandler($this->app['monolog'], 60));
        $trackerHandlers = array();

        //Also output to console unless quiet is set
        if ( ! $input->getOption('quiet')) {
            $trackerHandlers[] = new TrackerConsoleHandler($output);
        }

        //Setup a task tracker
        $tracker = new Tracker($trackerHandlers, $limit ?: Tracker::Unknown);

        //Add the task tracker and run the indexer
        $this->app['indexer']->setTaskTracker($tracker);
        $this->app['indexer']->index($limit);
    }
}
/* EOF: Index.php */
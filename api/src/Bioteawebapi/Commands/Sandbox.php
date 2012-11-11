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
 * Clear all documents in the system
 */
class Sandbox extends Command
{
    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('sandbox')->setDescription('Sandbox for testing different strategies');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //See how long it takes to just go through all the files with no processing...
        $tracker = new Tracker(new TrackerConsoleHandler($output));

        //Load libraries
        $filemgr = $this->app['fileclient'];
        $builder = $this->app['builder'];

        //Do it with builder
        $tracker->start();
        while ($docPath = $filemgr->getNextFile()) {

            $doc = $builder->buildDocument($docPath);
            unset($doc);

            $tracker->tick();
        }
        $tracker->finish();
    }
}

/* EOF: Sandbox.php */
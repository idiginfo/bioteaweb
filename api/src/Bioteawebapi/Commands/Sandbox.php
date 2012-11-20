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

use TaskTracker\Tracker, TaskTracker\Tick;
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
        $this->addArgument('file', InputArgument::REQUIRED, "CSV file with PMIDs");
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //See how long it takes to just go through all the files with no processing...
        $tracker = new Tracker(new TrackerConsoleHandler($output));

        //Array to hold pmids
        $pmids = array();

        //Array to hold intersect
        $intersect = array();

        //Read from the file into an array
        $fh = fopen($input->getArgument('file'), 'r');
        if ( ! $fh) {
            throw new \InvalidArgumentException("Could not read from file: " . $input->getArgument('file'));
        }

        //No headers; data starts immediately
        while ($row = fgetcsv($fh)) {
            $pmids[] = $row[1];
        }

        //Go through files and find intersect
        $tracker->start();
        while ($docPath = $this->app['fileclient']->getNextFile()) {

            $filename = basename($docPath);

            if (in_array($filename, $pmids)) {
                $intersect[] = $filename;
                $tracker->tick('Comparing...');
            }
            else {
                $tracker->tick('Comparing...', Tick::SKIP);
            }            
        }
        $tracker->finish();

        //Dump report
        $output->writeln("Matching:");
        foreach ($intersect as $pmid) {
            $output->writeln($pmid);
        }
    }
}

/* EOF: Sandbox.php */
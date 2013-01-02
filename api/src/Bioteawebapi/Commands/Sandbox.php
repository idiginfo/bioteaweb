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

use SimpleXMLElement;

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
        //Tracker
        $tracker = new Tracker(new TrackerConsoleHandler($output));

        //Go through files and find intersect
        $tracker->start();
        while ($docPath = $this->app['fileclient']->getNextFile()) {

            //Get the fullpath
            $fullPath = $this->app['fileclient']->resolvePath($docPath);

            //Build the XML object
            $xml = new SimpleXMLElement($fullPath, 0, true);
            $xml->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
            $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

            $totalCount = 0;

            foreach ($xml->xpath("//rdf:Description") as $sec) {

                //Attempt to get it from the hasTopic['rdf:resource'] attribute
                $about = (string) $sec[0]->attributes('rdf', true)->about;

                $canEndWith = array(
                    '/Materials',
                    '/Methods', 
                    '/Materials-and-methods'
                );

                foreach($canEndWith as $cew) {

                    $len = strlen($cew) * -1;
                    if (strcasecmp($cew, substr($about, $len)) == 0) {
                        $totalCount++;
                        break;
                    }
                }
            }

            $tracker->tick("Processing");    
        }
        $tracker->finish("Total Count: " . number_format($totalCount, 0));
    }
}

/* EOF: Sandbox.php */
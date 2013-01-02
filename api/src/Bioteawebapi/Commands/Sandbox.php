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
    /**
     * @var array
     */
    private $canEndWith = array(
        '/Materials',
        '/Methods', 
        '/Materials-and-methods'
    );

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

        //Number of documents where Meterials/Methods found
        $totalCount = 0;

        //Go through files and find intersect
        $tracker->start();
        while ($docPath = $this->app['fileclient']->getNextFile()) {

            //Get the fullpath
            $fullPath = $this->app['fileclient']->resolvePath($docPath);

            if ($this->findMaterialsMethods($fullPath)) {
                $totalCount++;
            }

            $tracker->tick(sprintf("Processing (%s found)", number_format($totalCount, 0)));
        }

        $tracker->finish("Total Count: " . number_format($totalCount, 0));
    }

    // --------------------------------------------------------------

    private function findMaterialsMethods($filePath)
    {
        $xml = new SimpleXMLElement($filePath, 0, true);
        $xml->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
        $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

        //Go through each description
        foreach ($xml->xpath("//rdf:Description") as $sec) {

            //Attempt to get the 'rdf:about' attribute of the item
            $about = (string) $sec[0]->attributes('rdf', true)->about;

            foreach($this->canEndWith as $cew) {
                $len = strlen($cew) * -1;
                if (strcasecmp($cew, substr($about, $len)) == 0) {
                    return true;
                }
            }
        }

        //If made it here...
        return false;
    }    
}

/* EOF: Sandbox.php */
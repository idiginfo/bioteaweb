<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

use TaskTracker\Tracker, TaskTracker\Tick, 
    TaskTracker\OutputHandler\SymfonyConsole as TrackerConsole;

/**
 * RDF Loader
 *
 * @TODO: CHANGES
 * - Make the synchronous option work (already built the Task\LoadRdfFile class)
 */
class RdfLoad extends Command
{
    /**
     * @var Bioteardf\Service\FilesService
     */
    private $filesvc;

    /**
     * @var Bioteardf\Service\RdfLoader
     */
    private $rdfloader;

    // --------------------------------------------------------------

    protected function configure()
    {
        //Basic
        $this->setName('rdf:load');
        $this->setDescription('Load RDF(s) into the datastore from a directory or file');
        $this->setHelp("If a file is specified, it will be loaded.  If a directory is specified, it will be loaded recursively");

        //Arguments
        $this->AddArgument('source', InputArgument::REQUIRED, 'RDF file or directory to load from');

        //Options
        $this->addOption('limit',        'l', InputOption::VALUE_REQUIRED, 'Limit number of indexed files');
        $this->addOption('asynchronous', 'a', InputOption::VALUE_NONE,     'Perform the operation asynchronosuly (requires using the "worker" command)');
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->filesvc   = $app['files'];
        $this->rdfloader = $app['loader'];
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //File iterator
        $filepath = $input->getArgument('source');
        $sets = $this->filesvc->getIterator($filepath);

        //Limit
        $limit = $input->getOption('limit') ?: -1;

        //Tracker
        $tracker = new Tracker(new TrackerConsole($output, $limit));

        //Counters
        $numfiles = 0;
        $numtrips = 0;

        $tracker->start("Processing first set (this can take a few seconds)");

        //Load each file up
        foreach($sets as $set) {

            //Passed our limit?
            if ($limit > -1 && $numfiles >= $limit) {
                break;
            }

            //Do it
            $numtrips += $this->rdfloader->loadFileSet($set); 
            $numfiles++;

            //Progress bar
            $msg = sprintf(
                "Processed %s sets (%s triples)",
                number_format($numfiles, 0),
                number_format($numtrips, 0)
            );

            $tracker->tick($msg);
        }

        $tracker->finish();
    }
}

/* EOF: Test.php */
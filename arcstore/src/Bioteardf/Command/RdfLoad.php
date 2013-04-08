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
        $this->setName('rdf:load');
        $this->setDescription('Load RDF(s) into the datastore from a directory or file');
        $this->setHelp("If a file is specified, it will be loaded.  If a directory is specified, it will be loaded recursively");
        $this->AddArgument('source', InputArgument::REQUIRED, 'RDF file or directory to load from');
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
        $filepath = $input->getArgument('source');
        $files = $this->filesvc->getIterator($filepath);

        $tracker = new Tracker(new TrackerConsole($output));

        //Counters
        $numfiles = 0;
        $numtrips = 0;

        $tracker->start("Processing first record (this can take a few seconds)");

        //Load each file up
        foreach($files as $file) {

            //Skip directories
            if ($file->isDir()) {
                continue;
            }

            //Do it
            $numtrips += $this->rdfloader->loadFile($file); 
            $numfiles++;

            //Progress bar
            $msg = sprintf(
                "Processed %s files (%s triples)",
                number_format($numfiles, 0),
                number_format($numtrips, 0)
            );

            $tracker->tick($msg);
        }

        $tracker->finish();
    }
}

/* EOF: Test.php */
<?php

namespace Bioteardf\Command;

use Silex\Application;
use Bioteardf\Exception\BioteaRdfParseException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

use TaskTracker\Tracker, TaskTracker\Tick, 
    TaskTracker\OutputHandler\SymfonyConsole as TrackerConsole;

class Sandbox extends Command
{
    private $app;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('sandbox');
        $this->setDescription('A Sandbox');
        $this->setHelp("For messing around");
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        //Get reference to whole app
        $this->app = $app;

        $this->parser = $app['parser'];
        $this->parser->setDocObjectRegistryFactory($app['indexes.registry.factory.nodb']);
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$path = realpath('/vagrant/web/bioteaweb/api/tests/fixtures/rdfSampleFolder/PMC1134665.rdf');
        $path = realpath('/vagrant/web/bioteaweb/api/tests/fixtures/rdfSampleFolder');
        //$path = realpath('/vagrant/web/test');

        //Tracker
        $tracker = new Tracker(new TrackerConsole($output));

        foreach($this->app['files']->getIterator($path) as $set) {

            try {
                //Analyze it
                $objGraph = $this->parser->analyzeSet($set);

                $status = true;

                // foreach($objGraph['Topic'] as $t) {
                //     $output->writeln("URI: " . $t->uri . " ... HASH: " . $t->locallyUniqueId);
                // }

                //Persist it
                // $result = $this->app['persister']->persist($objGraph);
                // $output->writeln("Success");
            }
            catch (BioteaRdfParseException $e) {
                $status = false;
            }

            $tracker->tick("Processing...", $status);
        }

    }    
}

/* EOF: Sandbox.php */
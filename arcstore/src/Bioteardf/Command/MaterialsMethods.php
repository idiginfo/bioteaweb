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
 * RDF Loader Command
 */
class MaterialsMethods extends Command
{
    /**
     * @var Bioteardf\Service\FilesService
     */
    private $filesvc;

    /**
     * @var Bioteardf\Service\MiscDataStore
     */
    private $miscDataStore;

    /**
     * @var Minions\Client
     */
    private $minionsClient;

    /**
     * @var Bioteardf\Task\LoadRdfFile
     */
    private $loaderTask;

    // --------------------------------------------------------------

    protected function configure()
    {
        //Basic
        $this->setName('misc:mnm');
        $this->setDescription('Evaluate number of materials and methods in a set of RDF files');
        $this->setHelp("If a file is specified, it will be analyzed.  If a directory is specified, it will be analyzed recursively");

        //Arguments
        $this->AddArgument('source', InputArgument::REQUIRED, 'RDF file or directory to load from');

        //Options
        $this->addOption('limit',        'l', InputOption::VALUE_REQUIRED, 'Limit number of indexed files');
        $this->addOption('asynchronous', 'a', InputOption::VALUE_NONE,     'Perform the operation asynchronosuly (requires using the "worker" command)');
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->minionsClient = $app['minions.client'];
        $this->filesvc       = $app['files'];
        $this->miscDataStore = $app['misc_data'];
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //File iterator
        $filepath = $input->getArgument('source');
        $sets = $this->filesvc->getIterator($filepath);

        //Is asynchronous?
        $async = $input->getOption('asynchronous');

        //Limit
        $limit = $input->getOption('limit') ?: -1;

        //Tracker
        $tracker = new Tracker(new TrackerConsole($output, $limit));

        //Counters
        $numsets  = 0;
        $numtrips = 0;

        switch($async) {

            //If Asynchronous, load files into the queue to be processed by the LoadRdfFile Task
            case true:

                $tracker->start();

                //Enqueue each set
                foreach ($sets as $set) {

                    //Passed our limit?
                    if ($limit > -1 && $numsets >= $limit) {
                        break;
                    }

                    $this->minionsClient->enqueueNewTask('load_set', $set->toJson(), 'load_rdf');
                    $tracker->tick("Loading RDFs into Queue");
                    $numsets++;
                }

                $tracker->finish("Done loading");

                $queueSize = $this->minionsClient->getQueueSize('load_rdf');
                $output->writeln(sprintf("Loaded %s items for procsesing.  Run rdf:load:status to monitor loading progress", number_format($numsets, 0)));

            break;

            //If not, then load them one at a time synchronously
            case false:
            default:            

                $tracker->start("Evaluating first set (this can take a few seconds)");

                //Load each file up
                foreach($sets as $set) {

                    //Passed our limit?
                    if ($limit > -1 && $numsets >= $limit) {
                        break;
                    }

                    //Do it
                    $numtrips += $this->loader->loadFileSet($set);
                    $numsets++;

                    //Progress bar
                    $msg = sprintf(
                        "Processed %s sets (%s triples)",
                        number_format($numsets, 0),
                        number_format($numtrips, 0)
                    );

                    $tracker->tick($msg, ($numtrips !== false) ? Tick::SUCCESS : Tick::SKIP);
                }

                $tracker->finish();

            break;
        }
    }
}

/* EOF: MaterialsMethods.php */
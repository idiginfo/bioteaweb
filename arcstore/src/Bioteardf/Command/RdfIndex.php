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

use Bioteardf\Task\IndexRdfSet;

/**
 * RDF Indexer Command
 */
class RdfIndex extends Command
{
    /**
     * @var Bioteardf\Service\FilesService
     */
    private $filesvc;

    /**
     * @var Minions\Client
     */
    private $minionsClient;

    /**
     * @var Bioteardf\Task\IndexRdfSet
     */
    private $indexerTask;

    // --------------------------------------------------------------

    protected function configure()
    {
        //Basic
        $this->setName('rdf:index');
        $this->setDescription('Index RDF(s) into the index database from a directory or file');
        $this->setHelp("If a file is specified, it will be indexed.  If a directory is specified, it will be indexed recursively");

        //Arguments
        $this->AddArgument('source', InputArgument::REQUIRED, 'RDF file or directory to index from');

        //Options
        $this->addOption('limit',        'l', InputOption::VALUE_REQUIRED, 'Limit number of indexed files');
        $this->addOption('force-all',    'f', InputOption::VALUE_NONE,     'Index documents even if they have already been indexed before');        
        $this->addOption('asynchronous', 'a', InputOption::VALUE_NONE,     'Perform the operation asynchronosuly (requires using the "worker" command)');
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->filesvc       = $app['files'];
        $this->minionsClient = $app['minions.client'];
        $this->indexerTask   = $app['minions.tasks']['index_set'];
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

        //Force all?
        $force = (boolean) $input->getOption('force-all') ?: false;

        //Tracker
        $tracker = new Tracker(new TrackerConsole($output, $limit));

        //Counters
        $numsets  = 0;

        //Mode
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

                    $taskData = array('rdfSet' => $set->toJson(), 'force' => $force);
                    $this->minionsClient->enqueueNewTask('index_set', $taskData, 'index_rdf');
                    $tracker->tick("Loading RDF sets into Queue");
                    $numsets++;
                }

                $tracker->finish("Done loading");

                $queueSize = $this->minionsClient->getQueueSize('index_rdf');
                $output->writeln(sprintf("Loaded %s items for processing.  Run rdf:index:status to monitor indexing progress", number_format($numsets, 0)));

            break;

            //If not, then load them one at a time synchronously
            case false:
            default:            

                $tracker->start("Processing first set (this can take a few seconds)");

                //Load each file up
                foreach($sets as $set) {

                    //Passed our limit?
                    if ($limit > -1 && $numsets >= $limit) {
                        break;
                    }

                    //Do it
                    $taskData = array('rdfSet' => $set->toJson(), 'force' => $force);
                    $result = $this->indexerTask->run($taskData);
                    $numsets++;

                    //Result maps directly to TaskTracker\Tick Constants
                    $tracker->tick("Indexing RDF Sets", $result);
                }

                $tracker->finish();

            break;
        }
    }
}

/* EOF: RdfIndex.php */
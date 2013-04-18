<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * RDFLoad Status
 */
class RdfLoadStatus extends Command
{
    /**
     * @var Minions\Client
     */
    private $minionsClient;

    // --------------------------------------------------------------

    protected function configure()
    {
        //Basic
        $this->setName('rdf:load:status');
        $this->setDescription('Monitor asynchronous loading of RDFs loaded with `rdf:load -a`');
        $this->setHelp("This command is useful only if you are loading RDFs using the '-a' asynchronous option.  It reports on the number remaining to be loaded");
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->minionsClient = $app['minions.client'];
    } 

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Query the queue for the number of items remaining...
        $output->writeln("Queue size is " . number_format($this->minionsClient->getQueueSize('load_rdf'), 0));
    }

}

/* EOF: RdfLoadStatus.php */
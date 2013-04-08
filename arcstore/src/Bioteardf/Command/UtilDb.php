<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class UtilDb extends Command
{
    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var ARC2_Store
     */
    private $arcStore;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('util:db');
        $this->setDescription('Database utilites');
        $this->setHelp("Build the schema, clear the database, triples, or show database information");
        $this->AddArgument('action', InputArgument::REQUIRED, 'Action to take', 'info');
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->arcStore = $app['arc.store'];
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Set output to class property
        $this->output = $output;

        //Run
        switch($input->getArgument('action')) {
            case 'build':
                return $this->executeBuild();
            case 'info':
                return $this->executeInfo();
            case 'clear':
                return $this->executeClear();
            default:
                throw new \RuntimeException("Invalid action");
        }
    }

    // --------------------------------------------------------------

    private function executeBuild()
    {
        //Build ARC
        if ( ! $this->arcStore->isSetUp()) {

            $output->writeln("Setting up ARC2 Triplestore Tables...");

            $this->arcStore->setUp();
            $errs = $this->arcStore->getErrors();

            if ( ! empty($errs)) {
                throw new RuntimeException("Error setting up store:\n" . implode("\n", $errs));
            }

            $outout->write("[ success ]");
        }
        else {
            $output->writeln("ARC2 Triplestore Tables already setup.  Skipping.");
        }
        
    }

    // --------------------------------------------------------------

    private function executeClear()
    {
        //Clear ARC
        if ($this->arcStore->isSetup())
        {
            $output->writeln("Clearing ARC2 Triplestore Tables...");
            $this->arcStore->reset();
            $output->write("[ success ]");
        }
    }

    // --------------------------------------------------------------

    private function executeInfo()
    {
        //How to get the database size (in bytes)
        //SELECT table_schema, Sum(data_length + index_length) FROM information_schema.tables WHERE table_schema = 'bioteardf' GROUP BY table_schema;   
    }
}

/* EOF: Schema.php */
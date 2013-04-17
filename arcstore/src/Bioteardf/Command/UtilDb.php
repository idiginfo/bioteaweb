<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use RuntimeException;

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

    /**
     * @var BioteaRdf\Service\BioteaRdfSetTracker
     */
    private $fileTracker;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('util:db');
        $this->setDescription('Database utilites');
        $this->setHelp("Build the schema, clear the database, triples, or show database information");
        $this->AddArgument('action', InputArgument::REQUIRED, 'Action to take');
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->arcStore    = $app['arc2.store'];
        $this->fileTracker = $app['file_tracker'];
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
            case 'reset':
                return $this->executeReset();
            default:
                throw new \RuntimeException("Invalid action");
        }
    }

    // --------------------------------------------------------------

    private function executeBuild()
    {
        //Build ARC
        if ( ! $this->arcStore->isSetUp()) {

            $this->output->write("\nSetting up ARC2 Triplestore Tables...");

            $this->arcStore->setUp();
            $errs = $this->arcStore->getErrors();

            if ( ! empty($errs)) {
                throw new RuntimeException("Error setting up store:\n" . implode("\n", $errs));
            }

            $this->output->writeln("[ success ]");
        }
        else {
            $this->output->writeln("ARC2 Triplestore Tables already setup.  Skipping.");
        }

        //Build Tracker
        if ( ! $this->fileTracker->isSetUp()) {
            $this->output->write("\nSetting up tracking tables...");
            $this->fileTracker->setUp();
            $this->output->writeln("[ success ]");
        }
        else {
            $this->output->writeln("Tracking tables already setup.  Skipping");
        }
    }

    // --------------------------------------------------------------

    private function executeReset()
    {
        //Clear ARC
        if ($this->arcStore->isSetup()) {
            $this->output->write("Clearing ARC2 Triplestore Tables...");
            $this->arcStore->reset();
            $this->output->writeln("[ success ]");
        }

        //Clear Tracker
        if ($this->fileTracker->isSetUp()) {
            $this->output->write("Clearing Tracking Tables...");
            $this->fileTracker->reset();
            $this->output->writeln("[ success ]");
        }

        //Build
        $this->executeBuild();
    }

    // --------------------------------------------------------------

    private function executeInfo()
    {
        //How to get the database size (in bytes)
        //SELECT table_schema, Sum(data_length + index_length) FROM information_schema.tables WHERE table_schema = 'bioteardf' GROUP BY table_schema;   
    }
}

/* EOF: Schema.php */
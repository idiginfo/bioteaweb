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
class IndexReports extends Command
{
    /**
     * @var Bioteardf\Service\Indexes\ReportRunner
     */
    private $reportRunner;


    // --------------------------------------------------------------

    protected function configure()
    {
        //Basic
        $this->setName('indexes:report');
        $this->setDescription('Generate a report from the indexes');
        $this->setHelp("Generate a report from the index tables");

        //Arguments
        $this->AddArgument('reportname', InputArgument::OPTIONAL, 'The name of the report to run (omit for list of available reports');
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        $this->reportRunner = $app['indexes.reports'];
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //File iterator
        $reportName = $input->getArgument('reportname');

        if ( ! $reportName) {

            $output->writeln("Available Reports");
            $output->writeln("---------------------------------------------");
            foreach($this->reportRunner->getReportDescriptions() as $key => $desc) {
                $output->writeln($key . ": " . $desc);
            }
        }
    }
}

/* EOF: IndexReports.php */
<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use RuntimeException;

/**
 * Utility command for building schema resetting tables and getting information about the database
 */
class UtilDb extends Command implements EventSubscriberInterface
{
    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    private $callbackOutput;

    /**
     * @var Bioteardf\Service\DatabaseManager
     */
    private $dbManager;

    // --------------------------------------------------------------

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        parent::__construct();
        $dispatcher->addSubscriber($this);
    }

    // --------------------------------------------------------------

    public static function getSubscribedEvents()
    {
        return array('bioteardf.dbmgr_event' => array('onDbMgrEvent'));
    }

    // --------------------------------------------------------------

    public function onDbMgrEvent(GenericEvent $event)
    {
        $msg    = $event->getSubject();
        $result = $event->getArgument('result');

        if ($this->callbackOutput) {
            $this->callbackOutput->writeln(sprintf(
                "%s ... [ %s ]",
                $msg,
                ($result) ? "<fg=green>success</fg=green>" : "<fg=red>fail</fg=red>"
            ));
        }
    }

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
        $this->dbManager = $app['dbmgr'];
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Set output to class property
        $this->callbackOutput = $output;

        //Run
        switch($input->getArgument('action')) {
            case 'build':
                return $this->executeBuild();
            case 'info':
                return $this->executeInfo();
            case 'reset':
                return $this->executeReset();
            case 'clear':
                return $this->executeClear();
            default:
                throw new \RuntimeException("Invalid action");
        }
    }

    // --------------------------------------------------------------

    private function executeBuild()
    {
        $this->dbManager->build();
    }

    // --------------------------------------------------------------

    private function executeClear()
    {
        $this->dbManager->clear();        
    }

    // --------------------------------------------------------------

    private function executeReset()
    {
        $this->callbackOutput->writeln("\nClearing...\n");
        $this->executeClear();
        $this->callbackOutput->writeln("\nResetting schemas...\n");
        $this->executeBuild();
        $this->callbackOutput->write("\n");
    }

    // --------------------------------------------------------------

    private function executeInfo()
    {
        //How to get the database size (in bytes)
        //SELECT table_schema, Sum(data_length + index_length) FROM information_schema.tables WHERE table_schema = 'bioteardf' GROUP BY table_schema;   
    }
}

/* EOF: Schema.php */
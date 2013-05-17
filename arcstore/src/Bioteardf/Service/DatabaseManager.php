<?php

namespace Bioteardf\Service;

use ARC2_Store;
use Minions\Client as MinionsClient;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class for managing Databases
 */
class DatabaseManager
{
    /**
     * @var ARC2_Store
     */
    private $arcStore;

    /**
     * @var Minions\Client
     */
    private $minionsClient;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Bioteardf\Service\DoctrineEntityManager
     */
    private $dem;

    // --------------------------------------------------------------

    public function __construct(ARC2_store $arcStore, DoctrineEntityManager $dem, MinionsClient $minions)
    {
        $this->arcStore      = $arcStore;
        $this->minionsClient = $minions;
        $this->dem           = $dem;
    }

    // --------------------------------------------------------------

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    // --------------------------------------------------------------

    public function build()
    {
        //Build ARC
        if ( ! $this->arcStore->isSetUp()) {

            $this->arcStore->setUp();
            $errs = $this->arcStore->getErrors();

            if ( ! empty($errs)) {
                throw new RuntimeException("Error setting up store:\n" . implode("\n", $errs));
            }

            $this->dispatch("Setting up ARC2 Triplestore Tables");
        }
        else {
            $this->dispatch("ARC2 Triplestore Tables already setup");
        }

        //Build other tables
        if ( ! $this->dem->isSetup()) {
            $this->dispatch("Setting up all other tables");
            $this->dem->setup();
        }
        else {
            $this->dispatch("Other tables already setup");
        }
    }

    // --------------------------------------------------------------

    public function clear()
    {
        //Clear ARC
        if ($this->arcStore->isSetup()) {
            $this->arcStore->reset();
            $this->dispatch("Clearing ARC2 Triplestore Tables");
        }

        //Clear Other Service Tables
        if ($this->dem->isSetup()) {
            $this->dem->reset();
            $this->dispatch("Clearing all other tables");
        }
        

        //Clear Minions Queues
        $numQueues = count($this->minionsClient->listQueues());
        foreach($this->minionsClient->listQueues() as $queueName) {
            $this->minionsClient->clearQueue($queueName);
        }
        $this->dispatch(sprintf("Clearing %s asynchronous task queues", $numQueues));        
    }

    // --------------------------------------------------------------

    public function reset()
    {
        $this->clear();
        $this->build();
    }

    // --------------------------------------------------------------

    /**
     * Dispatch an event
     *
     * @param string   $msg
     * @param boolean  $success
     */
    private function dispatch($msg, $success = true)
    {
        if ($this->dispatcher) {
            $evt = new GenericEvent($msg, array('result' => (bool) $success));
            $this->dispatcher->dispatch('bioteardf.dbmgr_event', $evt);
        }
    }

    // --------------------------------------------------------------

    protected function updateOrmSchema()
    {
        $queries = $this->checkOrmSchema(true);


        return count($queries);
    }
   
}

/* EOF: DatabaseManager.php */
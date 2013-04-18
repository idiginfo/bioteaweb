<?php

namespace Bioteardf\Service;

use ARC2_Store;
use Minions\Client as MinionsClient;
use Bioteardf\Service\BioteaRdfSetTracker;

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
     * @var BioteaRdf\Service\BioteaRdfSetTracker
     */
    private $fileTracker;

    /**
     * @var Minions\Client
     */
    private $minionsClient;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    // --------------------------------------------------------------

    public function __construct(ARC2_store $arcStore, BioteaRdfSetTracker $tracker, MinionsClient $minions)
    {
        $this->arcStore      = $arcStore;
        $this->fileTracker   = $tracker;
        $this->minionsClient = $minions;
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

        //Build Tracker
        if ( ! $this->fileTracker->isSetUp()) {
            $this->fileTracker->setUp();
            $this->dispatch("Setting up tracking tables");
        }
        else {
            $this->dispatch("Tracking tables already setup");
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

        //Clear Tracker
        if ($this->fileTracker->isSetUp()) {
            $this->fileTracker->reset();
            $this->dispatch("Clearing Tracking Tables");
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
}

/* EOF: DatabaseManager.php */
<?php

namespace Bioteardf\Service;

use ARC2_Store;
use Minions\Client as MinionsClient;
use Bioteardf\Service\BioteaRdfSetTracker;
use Bioteardf\Helper\PersistableService;

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
     * @var arrya
     */
    private $persistableServices;

    /**
     * @var Minions\Client
     */
    private $minionsClient;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    // --------------------------------------------------------------

    public function __construct(ARC2_store $arcStore, MinionsClient $minions, array $persistableSvcs = array())
    {
        $this->arcStore      = $arcStore;
        $this->minionsClient = $minions;

        foreach($persistableSvcs as $svc) {
            $this->addPersistService($svc);
        }
    }

    // --------------------------------------------------------------

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    // --------------------------------------------------------------

    public function addPersistService(PersistableService $svc)
    {
        $this->persistableServices[] = $svc;
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

        //Build Other services
        foreach ($this->persistableServices as $svc) {
            if ( ! $svc->isSetUp()) {
                $svc->setUp();
                $this->dispatch(sprintf("Setting up tables for %s service", get_class($svc)));
            }
            else {
                $this->dispatch(sprintf("Tables for %s service already setup", get_class($svc)));
            }
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
        foreach ($this->persistableServices as $svc) {
            if ($svc->isSetUp()) {
                $svc->reset();
                $this->dispatch(sprintf("Clearing tables for %s service", get_class($svc)));
            }
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
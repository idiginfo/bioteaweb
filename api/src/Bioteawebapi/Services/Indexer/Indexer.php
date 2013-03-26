<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Services\Indexer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Bioteawebapi\Services\RDFFileClient;
use Bioteawebapi\Exceptions\MySQLClientException;
use Bioteawebapi\Entities\Document;

use LogicException;

/**
 * Indexer indexes RDF documents into MySQL and optionally SOLR
 */
class Indexer
{
    const INDEXED = 1;
    const FAILED  = 0;
    const SKIPPED = -1;

    /**
     * @var IndexBuilder
     */
    private $builder;

    /**
     * @var IndexPersister
     */
    private $persister;

    /**
     * @var RDFFileClient
     */
    private $files;

    /**
     * @var SolrClient
     */
    private $solr;

    /**
     * @var int
     */
    private $numIndexed;

    /**
     * @var int
     */
    private $numSkipped;

    /**
     * @var int
     */
    private $numFailed;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param IndexBuilder   $builder  Biotea Doc Builder
     * @param EntityManager  $em       Doctrine ORM Entity Manager
     */
    public function __construct(RDFFileClient $files, IndexBuilder $builder, IndexPersister $persister)
    {
        $this->files     = $files;
        $this->builder   = $builder;
        $this->persister = $persister;
    }

    // --------------------------------------------------------------

    /** 
     * Set the event dispatcher
     *
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    // --------------------------------------------------------------

    /**
     * SOLR Client is an optional dependency
     *
     * @param SolrClient $solr  SOLR Client object
     */
    public function setSolrClient(SolrClient $solr)
    {
        $this->solr = $solr;
    }

    // --------------------------------------------------------------

    /**
     * Get the number of items indexed for the last run
     *
     * @return int 
     */
    public function getNumIndexed()
    {
        return $this->numIndexed;
    }

    // --------------------------------------------------------------

    /** 
     * Get the number of items where indexing failed for the last run
     *
     * @return int
     */
    public function getNumFailed()
    {
        return $this->numFailed;
    }

    // --------------------------------------------------------------

    /**
     * Return the number of items skipped for the last run
     *
     * @return int
     */
    public function getNumSkipped()
    {
        return $this->numSkipped;
    }

    // --------------------------------------------------------------

    /**
     * Get the number of items processed (skipped, failed, and indexed)
     *
     * @return int
     */
    public function getNumProcessed()
    {
        return $this->getNumFailed() + $this->getNumIndexed() + $this->getNumSkipped();
    }

    // --------------------------------------------------------------

    /**
     * Run the indexer on the basepath
     *
     * @param  string|null  $path  Path to index
     * @param  int          $limit  0 for no limit
     * @return int          The number processed (skipped, failed, and indexed)
     */
    public function indexAll($limit = 0)
    {
        //Reset counts
        $this->numIndexed = 0;
        $this->numFailed  = 0;
        $this->numSkipped = 0;

        //Reset the directory iterator in the file client
        $this->files->resetFileIterator();

        //Inform the listeners that we're starting
        $this->dispatch('indexer.startall', $this->files);

        //Get document graphs until we run out of files
        while ($docPath = $this->files->getNextFile()) {

            //If passed limit, get out
            if ($limit && $this->getNumProcessed() >= $limit) {
                return;
            }

            try {

                //Check to see if the path is already in the database
                //before building the document (this is for performance)
                if ($this->persister->checkDocumentExistsByPath($docPath)) {
                    $result = self::SKIPPED;
                }
                else {

                    //Build the document
                    $doc = $this->builder->buildDocument($docPath);

                    //Process it
                    $result = $this->index($doc);
                }

            } catch (\Exception $e) {

                //Set result to failed                
                $result = self::FAILED;

            }

            //Increment the counters
            switch($result) {
                case self::FAILED:  $this->numFailed++; break;
                case self::SKIPPED: $this->numSkipped++; break;
                case self::INDEXED: $this->numIndexed++; break;
                default:
                    throw new LogicException("Invalid returned value from Indexer::process");
            }
        }

        //Inform the listeners that we're starting
        $this->dispatch('indexer.finishall', array(
            'processed' => $this->getNumProcessed(),
            'indexed'   => $this->numIndexed,
            'failed'    => $this->numFailed,
            'skipped'   => $this->numSkipped
        ));

        return $this->getNumProcessed();
    }

    // --------------------------------------------------------------

    /**
     * Indexes a single document object
     *
     * @param Entities\Document $document
     * @param Array $insertions
     * @return int  Skipped, Indexed
     */
    public function index(Document $document)
    {
        try {

            $this->dispatch('indexer.before', $document);

            //MySQL Index
            $mySQLResult = $this->persister->persistDocument($document);

            //SOLR Index for Terms - Optional
            $solrResult = ($this->solr)
                ? $this->solr->persistDocument($document)
                : false;

        } catch (\Exception $e) {
            $this->dispatch('indexer.fail', $e, array('document' => $document));
            throw $e;
        }

        //Return indexed
        $result = ($mySQLResult OR $solrResult) ? self::INDEXED : self::SKIPPED;
        $this->dispatch('indexer.after', $result, array('document' => $document));
        return $result;
    }


    // --------------------------------------------------------------

    /**
     * Dispatch an event
     *
     * @param string $eventName  Refer to Symfony Event dispatcher naming rules
     * @param string $subject    The event subject
     * @param array  $arguments  Any additional data, if desired
     */
    protected function dispatch($eventName, $subject, array $arguments = array())
    {
        if ($this->dispatcher) {
            $event = new GenericEvent($subject, $arguments);
            $this->dispatcher->dispatch($eventName, $event);
        }
    }    
}


/* EOF: Indexer.php */
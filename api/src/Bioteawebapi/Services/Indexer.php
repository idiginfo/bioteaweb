<?php

namespace Bioteawebapi\Services;
use Bioteawebapi\Exceptions\MySQLClientException;
use Bioteawebapi\Entities\Document;
use Doctrine\ORM\EntityManager;
use TaskTracker\Tracker;

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
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

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
     * @var \TaskTracker\Tracker
     */
    private $taskTracker;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param IndexBuilder   $builder  Biotea Doc Builder
     * @param EntityManager  $em       Doctrine ORM Entity Manager
     */
    public function __construct(IndexBuilder $builder, EntityManager $em)
    {
        $this->builder  = $builder;
        $this->em       = $em;
    }

    // --------------------------------------------------------------

    /**
     * Set an optional taskTracker
     *
     * @param \TaskTracker\Tracker
     */
    public function setTraskTracker(Tracker $taskTracker)
    {
        $this->taskTracker = $taskTracker;
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
     * return int
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
     * @param string $path  Path to index
     * @param int    $limit  0 for no limit
     * @return int   The number processed (skipped, failed, and indexed)
     */
    public function index($path, $limit = 0)
    {
        //Reset counts
        $this->numIndexed = 0;
        $this->numFailed  = 0;
        $this->numSkipped = 0;

        $traverser = $this->builder->getTraverser($path);
        while ($doc = $traverser->getNextDocument()) {

            //If passed limit, get out
            if ($limit && $this->getNumProcessed() >= $limit) {
                return;
            }

            //Process it
            $result = $this->processItem($doc);

            //Inform task tracker
            if ($this->taskTracker) {
                $this->taskTracker->tick(1, null, $result);
            }

            switch($result) {
                case self::FAILED:  $this->numFailed++; break;
                case self::SKIPPED: $this->numSkipped++; break;
                case self::INDEXED: $this->numIndexed++; break;
                default:
                    throw new \Exception("Invalid returned value from Indexer::process");
            }
        }

        return $this->getNumProcessed();
    }

    // --------------------------------------------------------------

    /**
     * Indexes a single document object
     *
     * @TODO: Also deal with SOLR exceptions?
     *
     * @param Entities\Document $document
     * @return int  Skipped, Indexed, or Failed
     */
    public function processItem(Document $document)
    {
        try {
            
            //Persist the document
            $numIndexed = $this->persistItem($document);

            //@TODO: ALSO INDEX w/SOLR HERE!

            //Return indexed
            return ($numIndexed > 0) ? self::INDEXED : self::SKIPPED;
        }
        catch (MySQLClientException $e) {
            return self::FAILED;
        }
    }

    // --------------------------------------------------------------

    /**
     * Persist a document object
     *
     * @param Entities\Document $document
     * @return int  Number of INSERTs performed
     */
    protected function persistItem(Document $document)
    {
        //Start with the vocabularies and work backards.
        $document->persist($this->em);

        //Determine number of changes
        $uow = $this->em->getUnitOfWork();
        $num = count($uow->getScheduledEntityInsertions());

        //Do it
        $this->em->flush();

        //Return the number of insertions
        return $num;
    }

}


/* EOF: Indexer.php */
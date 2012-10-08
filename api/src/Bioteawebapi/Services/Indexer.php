<?php

namespace Bioteawebapi\Services;
use Bioteawebapi\Models\BioteaDocSet;

/**
 * Indexer indexes RDF documents into SOLR and MySQL
 */
class Indexer
{
    const INDEXED = 1;
    const FAILED  = 0;
    const SKIPPED = -1;

    /**
     * @var DocSetBuidler
     */
    private $builder;

    /**
     * @var SolrClient
     */
    private $solr;

    /**
     * @var MySQLClient
     */
    private $db;

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

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param DocSetBuilder  $builder     Biotea Doc Builder
     * @param SolrClient     $solrClient  SOLR Client object
     * @param MySQLClient    $db          MySQL Client object
     */
    public function __construct(DocSetBuilder $builder, SolrClient $solr, MySQLClient $db)
    {
        $this->builder  = $builder;
        $this->solr     = $solr;
        $this->db       = $db;
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
        while ($obj = $traverser->getNextDocument()) {

            //If passed limit, get out
            if ($this->getNumProcessed() >= $limit) {
                return;
            }

            //Process it
            $result = $this->processItem($obj);

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
     * Process a file
     *
     * Builds a docSetObj from it and then attempts to index it
     *
     * @param Models\BioteaDocSet $docset
     * @return int  Skipped, Indexed, or Failed
     */
    public function processItem($docset)
    {
        //LEFT OFF HERE!
        //@TODO: Implement the indexing part of this class!

        return self::SKIPPED;
    }
}


/* EOF: Indexer.php */
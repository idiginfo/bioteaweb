<?php

namespace Bioteardf\Service;

use ARC2_RDFParser, ARC2_Store;
use RuntimeException, Closure;
use Bioteardf\Model\BioteaRdfSet;

class RdfLoader
{
    /**
     * @var ARC2_Store
     */
    private $arcStore;

    /**
     * @var boolean
     */
    private $dbSetup;

    /**
     * @var Bioteardf\Service\BioteaRdfSetTracker
     */
    private $tracker;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param ARC2_Store                            $arcStore
     * @param BioteaRdf\Service\BitoeaRdfSetTracker $tracker
     */
    public function __construct(ARC2_Store $arcStore, BioteaRdfSetTracker $tracker)
    {
        $this->arcStore = $arcStore;
        $this->tracker  = $tracker;
    }

    // --------------------------------------------------------------

    /**
     * Load RDF from file into triplestore
     *
     * @param string $filepath  Full path to RDF file
     * @return int  The number of triples inserted
     */ 
    public function loadFile($filepath)
    {
        if ( ! $this->dbSetup) {
            $this->checkDb();
        }

        //Ensure it is a string
        $filepath = (string) $filepath;

        //Check it
        if ( ! is_readable($filepath)) {
            throw new RuntimeException("Could not read file: " . $filepath);
        }

        //Read it
        $fContents = file_get_contents($filepath);

        //Insert
        $result = $this->arcStore->insert($fContents, 'biotea');
        return (isset($result['t_count'])) ? $result['t_count'] : 0;
    }

    // --------------------------------------------------------------

    /**
     * Load a set of RDF files
     *
     * @param Bitoeardf\Model\BioteaRdfSet $rdfSet
     * @return int|boolean  The number of triples inserted (false if skipped)
     */
    public function loadFileSet(BioteaRdfSet $rdfSet)
    {
        //Is already loaded?
        if ($this->tracker->isAlreadyLoaded($rdfSet)) {
            return false;
        }

        //Load it
        $numTrips = 0;
        foreach($rdfSet as $fp) {
            $numTrips += $this->loadFile($fp);
        }

        //Record as loaded
        $this->tracker->recordAsLoaded($rdfSet);

        //Get it
        return $numTrips;
    }   

    // --------------------------------------------------------------

    /**
     * Check if ARC is setup and set a flag
     *
     * @throws RuntimeException
     */
    private function checkDb()
    {
        if ( ! $this->arcStore->isSetUp()) {
            throw new RuntimeException("ARC2 Store is not setup!  Cannot load.");
        }

        if ( ! $this->tracker->isSetUp()) {
            throw new RuntimeException("Tracker database is not setup!  Cannot load.");
        }

        $this->dbSetup = true;
    }
}

/* EOF: RdfLoader.php */
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
    private $arcSetup;

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(ARC2_Store $arcStore)
    {
        $this->arcStore = $arcStore;
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
        if ( ! $this->arcSetup) {
            $this->checkArc();
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
     * @return int  The number of triples inserted
     */
    public function loadFileSet(BioteaRdfSet $rdfSet)
    {
        $numTrips = 0;

        foreach($rdfSet as $fp) {
            $numTrips += $this->loadFile($fp);
        }

        return $numTrips;
    }   

    // --------------------------------------------------------------

    /**
     * Check if ARC is setup and set a flag
     *
     * @throws RuntimeException
     */
    private function checkArc()
    {
        if ( ! $this->arcStore->isSetUp()) {
            throw new RuntimeException("ARC2 Store is not setup!  Cannot load.");
        }

        $this->arcSetup = true;
    }
}

/* EOF: RdfLoader.php */
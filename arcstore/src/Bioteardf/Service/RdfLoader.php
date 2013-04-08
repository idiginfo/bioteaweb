<?php

namespace Bioteardf\Service;

use ARC2_RDFParser, ARC2_Store;
use RuntimeException, Closure;

class RdfLoader
{
    /**
     * @var ARC2_Store
     */
    private $arcStore;

    /**
     * @var Closure
     */
    private $arcParserFactory;

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(Closure $arcParserFactory, ARC2_Store $arcStore)
    {
        $this->arcParserFactory = $arcParserFactory;
        $this->arcStore  = $arcStore;
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
     * Parse RDF from file
     *
     * @param string $filepath  Full path to RDF file
     * @return array  Array of triples
     */
    public function parseFile($filepath)
    {   
        $parser = $this->arcParser();

        //Ensure it is a string
        $filepath = (string) $filepath;

        //Read it
        if ( ! is_readable($filepath)) {
            throw new RuntimeException("Could not read file: " . $filepath);
        }

        //Parse
        $parser->parse($filepath);
        return $parser->getTriples();
    }


    // --------------------------------------------------------------

    /**
     * Shortcut method to invoke arcParser closure property
     */
    private function arcParser()
    {
        return $this->arcParserFactory->__invoke();
    }    
}

/* EOF: RdfLoader.php */
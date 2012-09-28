<?php

namespace Bioteawebapi\Services;
use EasyRdf_Graph as RdfGraph;
use Bioteawebapi\Models\BioteaRdfDocSet;

/**
 * Solr Indexer indexes RDF documents into SOLR
 */
class SolrIndexer
{
    /**
     * @var string  Path to use
     */
    private $basepath;

    /**
     * @var DocumentManager
     */
    private $solr;

    /**
     * @var int
     */
    private $numIndexed;

    /**
     * @var int
     */
    private $numFailed;

    /**
     * @var string
     */
    private $filePattern = "/^PMC[\d]+\.rdf$/";

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string  $path         Realpath to RDF files
     * @param Indexer $solrMgr      SOLR Client object
     */
    public function __construct($path, SolrClient $solr)
    {
        //Path check
        if ( ! is_readable($path) OR ! is_dir($path)) {
            throw new \InvalidArgumentException("The RDF file path is invalid: " . $path);
        }

        //Set properties
        $this->basepath = rtrim($path, DIRECTORY_SEPARATOR);
        $this->solr     = $solr;

        //Reset count
        $this->numIndexed = 0;
        $this->numFailed  = 0;
    }

    // --------------------------------------------------------------

    /**
     * Get the number of items indexed
     *
     * @return int 
     */
    public function getNumIndexed()
    {
        return $this->numIndexed;
    }

    // --------------------------------------------------------------

    /** 
     * @return int
     */
    public function getNumFailed()
    {
        return $this->getNumFailed;
    }

    // --------------------------------------------------------------

    /**
     * Run the indexer on the basepath
     *
     * @param int $limit    0 for no limit
     */
    public function index($limit = 0)
    {
        $this->limit = $limit;

        $this->runIndexer();

        $this->limit = null;
        return $this->numIndexed;
    }

    // --------------------------------------------------------------

    /**
     * Run the indexer does the actual work
     *
     * Recursive method
     *
     * @param string $path  Subpath
     */
    protected function runIndexer($path = '')
    {
        //Set fullpath
        $fullpath = realpath($this->basepath . '/' . $path);

        foreach (scandir($fullpath) as $file) {

            //Skip hidden
            if ($file{0} == '.') {
                continue;
            }

            //Reached limit? Done.
            if ($this->numIndexed + $this->numFailed > $this->limit) {
                break;
            }

            //Do it
            if (is_dir($fullpath . '/' . $file)) {
                $this->runIndexer($file);
            }
            elseif (preg_match($this->filePattern, $file)) { 
            
                if ($this->processFile($file)) {
                    $this->numIndexed++;
                }  
                else {
                    $this->numFailed++;
                }
            }
        }
    }

    // --------------------------------------------------------------

    protected function processFile($relativeFilePath)
    {
        $fullPath    = realpath($this->basepath . '/' . $relativeFilePath);
        $relDirPath  = ltrim(dirname($relativeFilePath), '.');
        $filename    = basename($fullPath, '.rdf');

        //Try to read the RDF
        try {

            //Main RDF
            $rdfGraph = new RdfGraph();
            $rdfGraph->parseFile($fullPath);

            //Build object
            $rdfObj = new BioteaRdfDocSet($rdfGraph, $relativeFilePath);

            //Add SubRDF Files
            $subfiles = array(
                'ncbo'     => $relDirPath . '/AO_annotations/' . $filename . '_ncboAnnotator.rdf',
                'whatizit' => $relDirPath . '/AO_annotations/' . $filename . '_whatizitUkPmcAll.rdf'
            );

            foreach($subfiles as $name => $relSubPath) {

                $relSubPath = ltrim($relSubPath, '/');
                $fullSubPath = $this->basepath . '/' . $relSubPath;

                if (file_exists($fullSubPath)) {
                    $subGraph = new RdfGraph();
                    $subGraph->parseFile($fullSubPath);
                    $rdfObj->addAnnotationFile($subGraph, $name, $relSubPath);
                }
            }

            //RDFObj
            $rdfObj->test();
        } 
        catch (\EasyRdf_Exception $e) {
            return false;
        }
    }
}


/* EOF: SolrIndexer.php */
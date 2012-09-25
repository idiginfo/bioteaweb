<?php

namespace Bioteawebapi\Models;
use \EasyRdf_Graph as RdfGraph;

/**
 * Represents a BioteaRdfDocSet
 */
class BioteaRdfDocSet
{
    /**
     * @param \EasyRdf_Graph
     */
    private $mainRdf;

    /**
     * @param string
     */
    private $mainFilePath;

    /**
     * @var array  Array of filenames  (annotationName)
     */
    private $annotationFileNames = array();

    /**
     * @var array  Array of RDFGraphs (filename => RDFGraph)
     */
    private $annotationRDFDocs = array();

    // --------------------------------------------------------------

    /**
     * Constructor
     * 
     * @param \EasyRdf_Graph $mainfile
     * @param string         $filepath  Relative to document basepath
     */
    public function __construct(RDFGraph $rdf, $filepath)
    {
        assert(is_string($filepath));
        $this->mainRdf      = $rdf;
        $this->mainFilePath = $filepath;
    }

    // --------------------------------------------------------------

    /**
     * Add Annotation File
     * 
     * @param \EasyRdf_Graph $mainfile
     * @param string         $annotationName    Name for the Annotation file
     * @param string         $filepath          Relative to document basepath
     */
    public function addAnnotationFile(RDFGraph $rdf, $annotationName, $filepath)
    { 
        assert(is_string($filepath));
        $this->annotationFileNames[$annotationName] = $filepath;
        $this->annotationRDFDocs[$filepath] = $rdf;
    }

    // --------------------------------------------------------------

    /**
     * Get the terms
     *
     * @return array
     */
    public function getTerms()
    {
        $termList = array();

        return $termList;
    }

    // --------------------------------------------------------------

    /**
     * Get the vocabularies
     *
     * @return array
     */
    public function getVocabularies()
    {
        $vocabList = array();

        return $vocabList;
    }

    // --------------------------------------------------------------

    /**
     * Get the topics
     *
     * @return array
     */
    public function getTopics()
    {
        $topicList = array();

        return $topicList;
    }

    // --------------------------------------------------------------

    /**
     * Get the main filepath
     *
     * @return string
     */
    public function getMainFilePath()
    {
        return $this->mainFilePath;
    }

    // --------------------------------------------------------------

    /**
     * Get the annotation file paths as incremental array
     *
     * @return array
     */
    public function getAnnotationFilePaths()
    {
        return array_values($this->annotationFilePaths);
    }
}

/* EOF: BioteaRdfDocSet */
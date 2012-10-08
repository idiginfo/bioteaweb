<?php

namespace Bioteawebapi\Services;
use Bioteawebapi\Models\BioteaDocSet;
use Bioteawebapi\Exceptions\DocSetBuilderException;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator;
use SimpleXMLElement;

/**
 * Doc Set Builder builds BioteaDocSets from files and optional traverses folders
 */
class DocSetBuilder
{
    /**
     * @var array
     */
    private $vocabularies = array();

    /**
     * @var string  Hardcoded filePatern to look for
     */
    private $filePattern = "/^PMC[\d]+\.rdf$/";

    /**
     * @var \RecurisveIteratorIterator
     */
    private $traverser;

    /**
     * @var string
     */
    private $traverserBasePath;

    // --------------------------------------------------------------

    public function __construct($vocabularies = array())
    {
        //Set vocabularies
        $this->setVocabularies($vocabularies);
    }

    // --------------------------------------------------------------

    /**
     * If indexing predefined vocabularies, set those here
     *
     * @param array  Keys are short names, values are long names
     */
    public function setVocabularies(Array $vocabularies)
    {
        $this->vocabularies = $vocabularies;
    }

    // --------------------------------------------------------------

    /**
     * Get a version of this object that can traverse directories
     *
     * @param  string $path  A full path
     * @return DocSetBuilder
     */
    public function getTraverser($path)
    {
        //Path check
        if ( ! is_readable($path) OR ! is_dir($path)) {
            throw new \InvalidArgumentException("The RDF file path is invalid: " . $path);
        }

        //Clone this, add traversal capabilities, and return it
        $that = clone $this;
        $that->traverserBasePath = realpath($path) . DIRECTORY_SEPARATOR;
        $that->traverser = new RecursiveIteratorIterator(new RDI($that->traverserBasePath));
        return $that;
    }

    // --------------------------------------------------------------

    /**
     * Interface to iterator for traverser
     *
     * Uses the directory iterator to find the next file, and if no more
     * files with the correct regex, return false
     *
     * @return BioteaDocSet|boolean  Returns false if no more files
     */
    public function getNextDocument()
    {
        //If this object is not set for traversing...
        if ( ! $this->traverserBasePath) {
            throw new \Exception("Traverser Not Available.  Use DocSetBuilder::getTraverser()!");
        }

        //Do it..
        $obj = false;

        //Get the next file, and see if it matches the regex pattern
        //otherwise, do it again until false or a valid regex
        while ($this->traverser->valid() && $obj === false) {
            $fileName = $this->traverser->getFileName();

            if (preg_match($this->filePattern, $fileName)) {
                
                //Get the full and relative paths
                $fullPath = dirname($this->traverser->getPathName());
                $relPath  = substr($fullPath, strlen($this->traverserBasePath)) . $fileName;
                $fullPath = $fullPath . DIRECTORY_SEPARATOR . $fileName;

                //Build the object
                $obj = $this->buildDocSet($fullPath, $relPath);
            }

            //Next item
            $this->traverser->next();            
        }

        return $obj;
    }

    // --------------------------------------------------------------

    /**
     * Build the BioteaDocSet Object from an Annotation file
     *
     * @param string $fullPath          The full system path to the file to parse
     * @param string $relativeFilePath  A relative file path to the file to parse
     * @return Bioteawebapi\Models\BioteaDocSet
     */
    public function buildDocSet($fullPath, $relativeFilePath)
    {
        //Check path
        if ( ! is_readable($fullPath)) {
            throw new DocSetBuilderException("The filepath does not exist: " . $fullPath);
        }

        //Paths
        $relDirPath  = ltrim(dirname($relativeFilePath), '.');
        $filename    = basename($fullPath, '.rdf');

        //Build object
        $docSetObj = new BioteaDocSet($relativeFilePath, $this->vocabularies);

        //See if we have associated annotation files
        $subfiles = array(
            'ncbo'     => $relDirPath . '/AO_annotations/' . $filename . '_ncboAnnotator.rdf',
            'whatizit' => $relDirPath . '/AO_annotations/' . $filename . '_whatizitUkPmcAll.rdf'
        );

        //If so, add them to the object
        foreach($subfiles as $name => $relSubPath) {

            $relSubPath = ltrim($relSubPath, '/');
            $fullSubPath = dirname($fullPath) . '/' . $relSubPath;

            if (file_exists($fullSubPath)) {
                $xml = new SimpleXMLElement($fullSubPath, 0, true);
                $docSetObj->addAnnotationFile($xml, $name, $relSubPath);
            }
        }

        return $docSetObj;
    }
}

/* EOF: BitoeaDocSetBuilder.php */
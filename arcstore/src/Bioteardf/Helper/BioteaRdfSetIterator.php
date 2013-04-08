<?php

namespace Bioteardf\Helper;

use RegexIterator, Iterator, SplFileInfo;
use Bioteaardf\Model\BioteaRdfSet;

class BioteaRdfSetIterator extends RegexIterator
{
    /**
     * REGEX Pattern to identify main RDF files in a set
     */
    const REGEX = "#.+/PMC[\d]+\.rdf$#i";

    /**
     * Relative path of the annotations files to the main RDF file path
     */
    const AOPATH = 'AO_annotations/';

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator, self::REGEX);
    }

    // --------------------------------------------------------------

    /**
     * Override parent::current() to get entire set
     *
     * @return Bioteardf\Model\BioteaRdfSet
     */
    public function current()
    {
        $curr = parent::current();
        return new BioteaRdfSet($curr, $this->findAnnotationFiles($curr));
    }

    // --------------------------------------------------------------

    /**
     * Get the Annotation Files associated with a main file
     *
     * @param SplFileInfo
     * @return array  Array of SplFileInfo Objects for associatd annotation files
     */
    private function findAnnotationFiles(SplFileInfo $fileinfo)
    {
        $aopath = $fileinfo->getPath() . '/' . trim(self::AOPATH, '/') . '/';

        $arr = array();
        foreach(glob($aopath . $fileinfo->getBasename('.rdf') . '_*.rdf') as $f) {
            $arr[] = new SplFileInfo($f); 
        }
        return $arr;
    }
}

/* EOF: BioteaRdfSetIterator.php */
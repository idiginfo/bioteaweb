<?php

namespace Bioteardf\Service;

use Bioteardf\Helper\BioteaRdfSetIterator;
use Bioteardf\Model\BioteaRdfSet;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator;
use SplFileInfo;
use ArrayIterator;
use RuntimeException;

/**
 * Class for interacting with RDF Files
 */
class RdfFileService
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
     * Return an iterator whether the filepath is a directory or a single file
     *
     * @return Iterator of SPLFileInfo Objects
     */
    public function getIterator($filepath)
    {
        if ( ! is_readable($filepath)) {
            throw new RuntimeException("Could not read directory or file: " . $filepath);
        }

        return (is_dir($filepath))
            ? new BioteaRdfSetIterator(new RecursiveIteratorIterator(new RDI($filepath)), $this, self::REGEX)
            : new ArrayIterator(array(new SplFileInfo($filepath)));
    }

    // --------------------------------------------------------------

    /**
     * Return a RDF set
     *
     * @param string $mainFile  Full path to main file
     */
    public function buildRdfSet($mainFile)
    {
        $curr = new SplFileInfo($mainFile);
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

/* EOF: FileService.php */
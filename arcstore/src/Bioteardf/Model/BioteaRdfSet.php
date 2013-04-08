<?php

namespace Bioteardf\Model;

use SplFileInfo, IteratorAggregate, Countable;

/**
 * Represents an atomic set of Bitoea RDF files
 */
class BioteaRdfSet implements IteratorAggregate, Countable
{
    /**
     * @var string
     */
    protected $mainFile;

    /**
     * @var array
     */
    protected $annotationFiles;

    /**
     * @var string
     */
    protected $md5;

    // --------------------------------------------------------------

    public function __construct(SplFileInfo $mainFile, array $annotationFiles = array())
    {
        //Add mainfile
        $this->mainFile = $mainFile;
        $this->md5 = md5((string) $this->mainFile);

        //Add annotation files
        foreach($annotationFiles as $af) {
            $this->addAnnotationFile($af);
        }
    }

    // --------------------------------------------------------------

    public function __get($item)
    {
        return $this->$item;
    }    

    // --------------------------------------------------------------

    public function __tostring()
    {
        return (string) $this->mainFile;
    }

    // --------------------------------------------------------------

    public function getIterator()
    {
        return array_merge(array($this->mainFile), array_values($this->annotationFiles));
    }

    // --------------------------------------------------------------

    public function count()
    {
        return count($this->getIterator());
    }

    // --------------------------------------------------------------

    private function addAnnotationFile(SplFileInfo $file)
    {
        $this->annotationFiles[] = $file;
    }
}

/* EOF: BioteaRdfSet.php */
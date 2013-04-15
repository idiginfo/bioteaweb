<?php

namespace Bioteardf\Model;

use SplFileInfo, IteratorAggregate, Countable,
    ArrayIterator;

/**
 * Entity Object - Represents an atomic set of Bitoea RDF files
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

    /**
     * Constructor
     *
     * @param SplFileInfo $mainFile
     * @param array       $annotationFiles  Array of SplFileInfo objects
     */
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

    /**
     * @param string $item
     * @return mixed
     */
    public function __get($item)
    {
        return $this->$item;
    }    

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function toJson()
    {
        $data = array(
            'mainFile'        => (string) $this->mainFile,
            'annotationFiles' => array_map(function($v) { return (string) $v; }, $this->annotationFiles),
            'md5'             => $this->md5
        );

        return json_encode($data);
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function __tostring()
    {
        return (string) $this->mainFile;
    }

    // --------------------------------------------------------------

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator(array_merge(array($this->mainFile), array_values($this->annotationFiles)));
    }

    // --------------------------------------------------------------

    /**
     * @return int
     */
    public function count()
    {
        return count($this->getIterator());
    }

    // --------------------------------------------------------------

    /**
     * @param SplFileInfo $file
     */
    private function addAnnotationFile(SplFileInfo $file)
    {
        $this->annotationFiles[] = $file;
    }
}

/* EOF: BioteaRdfSet.php */
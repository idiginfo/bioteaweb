<?php

namespace Bioteardf\Model;

use Bioteardf\Helper\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use SplFileInfo, IteratorAggregate, Countable,
    ArrayIterator;

/**
 * Represents an atomic set of Biotea RDF files
 *
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="pmid", columns={"pmid"})
 * })  
 */
class BioteaRdfSet extends BaseEntity implements IteratorAggregate, Countable
{        
    /**
     * @var string
     * @Column(type="string") 
     */
    protected $mainFile;

    /**
     * @var array  Public interface works like an array, but is persisted as JSON
     * @Column(type="string") 
     */
    protected $annotationFiles;

    /**
     * @var string
     * @Column(type="string") 
     */
    protected $pmid;

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
        $this->pmid = substr($mainFile->getBaseName('.' . $mainFile->getExtension()), 3);
        $this->mainFile = $mainFile;

        //Add annotation files
        foreach($annotationFiles as $af) {
            $this->addAnnotationFile($af);
        }
    }

    // --------------------------------------------------------------

    /**
     * Get magic method
     *
     * @param string $item
     * @return mixed
     */
    public function __get($item)
    {
        switch($item) {
            case 'annotationFiles':
                return $this->getAnnotationFiles();
            default:
                return $this->$item;
        }
    }    

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function toJson()
    {
        $data = array(
            'mainFile'        => (string) $this->mainFile,
            'annotationFiles' => $this->getAnnotationFiles(false),
            'pmid'            => $this->pmid
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
     * Get Annotation Files
     * 
     * @param boolean $asObjects
     * @return array  Array of SplFileInfo objects or just strings
     */
    private function getAnnotationFiles($asObjects = true)
    {
        $afiles = json_decode($this->annotationFiles, true);

        if ( ! $afiles) {
            return array();
        }

        if ($asObjects) {
            $afiles = array_map(function($v) {
                return new SplFileInfo($v);
            }, $afiles);
        }

        return $afiles;
    }

    // --------------------------------------------------------------

    /**
     * Add an Annotation File
     *
     * @param SplFileInfo $file
     */
    private function addAnnotationFile(SplFileInfo $file)
    {
        $afiles = json_decode($this->annotationFiles, true) ?: array();
        $afiles[] = (string) $file;
        $this->annotationFiles = json_encode($afiles);
    }
}

/* EOF: BioteaRdfSet.php */
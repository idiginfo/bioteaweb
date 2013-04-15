<?php

namespace Bioteardf\Helper;

use RegexIterator, Iterator, SplFileInfo;
use Bioteardf\Service\RDFFileService;

class BioteaRdfSetIterator extends RegexIterator
{
    /**
     * @var Bioteardf\Service\RDFFileService
     */
    private $fileSvc;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Iterator                         $iterator
     * @param Bioteardf\Service\RDFFileService $fileSvc
     * @param string                           $regex
     */
    public function __construct(Iterator $iterator, RDFFileService $fileSvc, $regex)
    {
        parent::__construct($iterator, $regex);

        $this->fileSvc = $fileSvc;
    }

    // --------------------------------------------------------------

    /**
     * Override parent::current() to get a BioteaRdfSet object
     *
     * @return Bioteardf\Model\BioteaRdfSet
     */
    public function current()
    {
        return $this->fileSvc->buildRdfSet(parent::current());  
    }

}

/* EOF: BioteaRdfSetIterator.php */
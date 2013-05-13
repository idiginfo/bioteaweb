<?php

namespace Bioteardf\Service;

use Bioteardf\Model\BioteaRdfSet;
use Bioteardf\Model\TermsTopicsVocabs as TermsTopicsVocabsModel;
use SimpleXMLElement;

class TermsTopicsVocabs
{
    /**
     * @var array
     */
    private $vocabList;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array  Key/value vocabulary list [shorname => full URI, etc.]
     */
    public function __construct(array $vocabList)
    {
        $this->setVocabList($vocabList);
    }

    // --------------------------------------------------------------

    /**
     * Set the vocabulary list
     *
     * @param array  Key/value vocabulary list [shorname => full URI, etc.]
     */
    public function setVocabList(array $vocabList)
    {
        $this->vocabList = $vocabList;
    }

    // --------------------------------------------------------------

    /**
     * @return Biotaerdf\Model\TermsTopicsVocabs
     */
    public function analyze()
    {
        $ttv = new TermsTopicsVocabsModel;

        //Magic happens here...
        //LEFT OFF HERE LEFT OFF HERE LEFT OFF HERE

        $ttv->lock();
        return $ttv;
    }

}

/* EOF: TermsTopicsVocabs.php */
<?php

namespace Bioteardf\Model;

/**
 * Represents terms topics and vocabularies data for a corresponding BioteaRdfSet
 *
 * These things can be pretty memory-intensive, so remove them from memory ASAP.
 */
class TermsTopicsVocabs
{
    /**
     * @var boolean
     */ 
    private $locked = false;

    /**
     * @var string  Which document this refers to
     */
    private $md5;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $uniqueCounts;

    // --------------------------------------------------------------

    public function __construct($md5, $data)
    {
        $this->md5 = $md5;
    }

    // --------------------------------------------------------------

    public function lock()
    {
        $this->locked = true;
    }

    // --------------------------------------------------------------

    public function addTermTopicVocabSet($vocab, $topic, $term)
    {
        if ($this->locked) {
            throw new RuntimeException("Cannot modify locked TermsTopicsVocabs object");
        }

        if (isset($this->data[$vocab][$topic][$term])) {
            $this->data[$vocab][$topic][$term]++;
        }
        else {
            $this->data[$vocab][$topic][$term] = 1;
        }
    }

    // --------------------------------------------------------------

    /** 
     * Get data hierarchally
     *
     * It looks like:
     * array(
     *    'vocab1' => array(
     *       'topic1' => array(
     *         'term'  => 5,
     *         'term2' => 1
     *       )
     *    )
     * );
     *
     * @retun array
     */
    public function getData()
    {
        return $this->data;
    }
}

/* EOF: TermsTopicsVocabs.php */
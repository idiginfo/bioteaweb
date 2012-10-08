<?php

namespace Bioteawebapi\Models;

/**
 * Bitoea term value object
 */
class BioteaTerm
{
    /**
     * @var array  Array of BioteaTopic objects
     */
    private $topics = array();

    /**
     * @var string
     */
    private $termName;

    // --------------------------------------------------------------

    /**
     * Construct
     *
     * @param string $termName
     */
    public function __construct($termName, $topics = array())
    {
        $this->termName = $termName;
        $this->setTopics($topics);
    }

    // --------------------------------------------------------------

    /**
     * toString Magic Method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    // --------------------------------------------------------------

    /**
     * Get the term name
     *
     * @return string
     */
    public function getName()
    {
        return $this->termName;
    }

    // --------------------------------------------------------------

    /**
     * Set topics
     *
     * @param array $topics  Array of BioteaTopic objects
     */
    public function setTopics($topics)
    {
        array_map(array($this, 'addTopic'), $topics);
    }

    // --------------------------------------------------------------

    /**
     * Add topic
     *
     * @param Bioteatopic $topic
     */
    public function addTopic(BioteaTopic $topic)
    {
        $this->topics[] = $topic;
    }

    // --------------------------------------------------------------

    /**
     * Get topics
     *
     * @return array  Array of BioteaTopic objects
     */
    public function getTopics()
    {
        return $this->topics;
    }
}

/* EOF: BioteaTerm.php */
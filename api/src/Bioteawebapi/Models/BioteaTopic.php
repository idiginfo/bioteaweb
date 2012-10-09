<?php

namespace Bioteawebapi\Models;

/**
 * Biotea Topic value object
 */
class BioteaTopic
{
    /**
     * @var string
     */
    private $topicUri;

    /**
     * @var string
     */
    private $topicShortName;

    /**
     * @var string
     */
    private $vocabularyUri;

    /**
     * @var string
     */
    private $vocabularyShortName;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $topicUri
     * @param string $shortName
     * @param string $vocabUri
     * @param string $vocabShortName
     */
    public function __construct($topicUri, $shortName = null, $vocabUri = null, $vocabShortName = null)
    {
        $this->topicUri = $topicUri;

        $this->setVocabularyUri($vocabUri);
        $this->setTopicShortName($shortName);
        $this->setVocabularyShortName($vocabShortName);
    }

    // --------------------------------------------------------------

    /**
     * toString Magic Method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTopicUri();
    }

    // --------------------------------------------------------------

    public function setVocabularyUri($uri)
    {
        $this->vocabularyUri = $uri;
    }

    // --------------------------------------------------------------

    public function getVocabularyUri()
    {
        return $this->vocabularyUri;
    }

    // --------------------------------------------------------------

    public function setVocabularyShortName($shortName)
    {
        $this->vocabularyShortName = $shortName;
    }

    // --------------------------------------------------------------

    public function getVocabularyShortName()
    {
        return $this->vocabularyShortName;
    }

    // --------------------------------------------------------------

    public function getTopicUri()
    {
        return $this->topicUri;
    }

    // --------------------------------------------------------------

    public function setTopicShortName($name)
    {
        $this->topicShortName = $name;
    }

    // --------------------------------------------------------------

    public function getTopicShortName()
    {
        return $this->topicShortName;
    }
}

/* EOF: BioteaTopic.php */
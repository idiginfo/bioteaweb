<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\DocIndexEntity;

/**
 * Topic
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="uri", columns={"uri"})
 * })  
 */
class Topic extends DocIndexEntity
{  
    /**
     * @var string
     * @Column(type="string") 
     */
    protected $uri;

    /**
     * @var Term
     * @ManyToOne(targetEntity="Term", inversedBy="topics")
     */
    protected $term;

    /**
     * @var Vocabulary
     * @ManyToOne(targetEntity="Vocabulary", inversedBy="topics")
     */
    protected $vocabulary;

    // ----------------------------------------------------------------

    public function __construct($uri, Vocabulary $vocabulary, Term $term)
    {
        $this->uri = $uri;
        $this->vocabulary = $vocabulary;
        $this->term       = $term;

        $this->locallyUniqueId = (string) $this;  
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->uri;
    }
}

/* EOF: Topic.php */
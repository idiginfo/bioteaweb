<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\BaseEntity;

/**
 * Term
 * 
 * @Entity
 */
class Term extends BaseEntity
{
    /**
     * @var string
     * @Column(type="string") 
     */
    protected $term;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Annotation", mappedBy="terms")
     */
    protected $annotations;

    /**
     * @var ArrayCollection    
     * @OneToMany(targetEntity="Topic", mappedBy="terms")
     */
    protected $topics;

    // --------------------------------------------------------------

    public function __construct($term)
    {
        $this->term = $term;
        $this->annotations = new ArrayCollection();
        $this->topics      = new ArrayCollection();
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->term;
    }

    // --------------------------------------------------------------

    public function addTopic(Topic $topic)
    {
        $this->addSingle($topic, $this->topics);
    }

    // --------------------------------------------------------------

    public function addAnnotation(Annotation $annotation)
    {
        $this->annotations[] = $annotation;
    }    
}

/* EOF: Term.php */
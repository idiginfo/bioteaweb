<?php

namespace Bioteawebapi\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Topic Entity
 * 
 * @Entity
 */
class Topic
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $uri;

    /** @Column(type="string", nullable=true) **/
    protected $shortName; 

    /**
     * @ManyToOne(targetEntity="Vocabulary", inversedBy="topics")
     **/
    private $vocabulary;

    /**
     * @ManyToMany(targetEntity="Term", inversedBy="topics")
     * @JoinTable(name="JoinTermsTopics")
     **/
    private $terms;

    // --------------------------------------------------------------

    public function __construct($uri, $shortName = null, $vocabularyUri = null, $vocabularyShortname = null)
    {
        $this->terms = new ArrayCollection();

        //Vocabulary URI
        //LEFT OFF HERE -- Moving form Vocabs to Documents,
        //and then fixing up the Document and the builder and finally the indexer!
        //Try not to change the indexer and builder APIs too much.
        //Everything else is fair game
        if ($vocabularyUri && $vocabularyShortname) {
            $this->vocabulary = new Vocabulary($vocabularyUri, $vocabularyShortname);
        }
    }

}

/* EOF: Topic.php */
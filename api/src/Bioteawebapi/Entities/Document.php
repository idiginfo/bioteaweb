<?php

namespace Bioteaweb\Entities;

/**
 * Document Entity represents Indexes for a BioteaDocument
 * 
 * @Entity
 */
class Document
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $rdfPath;

    /** @Column(type="string") **/
    protected $rdfAnnotationPaths;

    /** **/
    protected $terms;

    /** @return array Array of Vocabularies */
    public function getVocabularies();

    /** @return array Array of Topics */
    public function getTopics();

    /** @return array  Array of Terms */
    public function getTerms();
    public function getRDFPath();

    /** @return array */
    public function getRDFAnnotationPaths();
}

/* EOF: Document.php */
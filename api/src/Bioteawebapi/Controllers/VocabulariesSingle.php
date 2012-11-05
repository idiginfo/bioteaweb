<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;

/**
 * Single vocabulary info Controller
 */
class VocabulariesSingle extends Abstracts\SingleController
{
   /**
     * @var Bioteawebapi\Entities\Term
     */
    private $vocabObj = null;

    // --------------------------------------------------------------

    /**
     * Get term based off its name
     *
     * @inherit
     */
    protected function getMainItem($identifier)
    {
        //Get it from the db
        $vocabObj = array_shift($this->app['dbclient']->getVocabularies($identifier));

        //Set it to a class property
        $this->vocabObj = $vocabObj;

        return $vocabObj ? $vocabObj->toArray() : false;
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getMainItemName()
    {
        return "Vocabularies";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array('/vocabularies/{vocabulary}' => "Get information about a single vocabulary and its related items");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "Returns information about a single vocabulary in HTML";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "Returns information about a single vocabulary in JSON";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRelatedItems()
    {
        return array(
            'documents' => "Documents",
            'topics'    => "Topics",
            'terms'     => "Terms"
        );
    }

    // --------------------------------------------------------------

    protected function getRelatedItemsCount($which)
    {
        switch ($which) {
            case 'topics':
                return $this->vocabObj->getTopics()->count();
            break;
            case 'terms':
                return $this->vocabObj->getTerms()->count();
            break;
            case 'documents':
                return $this->vocabObj->getDocuments()->count();
            break;
        }
    }

    // --------------------------------------------------------------

    /**
     * Get related items
     *
     * @param string $which
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function getRelatedItems($which, $offset, $limit)
    {
        $items = array();

        switch ($which) {

            case 'topics':
                $topics = $this->vocabObj->getTopics()->slice($offset, $limit);
                foreach($topics as $topicObj) {
                    $items[] = $topicObj->toArray();
                }

            break;
            case 'terms':

                $terms = $this->vocabObj->getTerms()->slice($offset, $limit);
                foreach($terms as $termObj) {
                    $items[] = $termObj->toArray();
                }

            break;
            case 'documents':

                $docs = $this->vocabObj->getDocuments()->slice($offset, $limit);
                foreach($docs as $docObj) {
                    $items[] = $this->buildDocumentDetails($docObj);
                }

            break;
        }     

        return $items;
    }

    // --------------------------------------------------------------

    /**
     * Inputs a document class and outputs the URLS for the RDF files
     * 
     * @param Bioteawebapi\Entities\Document
     * @return array
     */
    protected function buildDocumentDetails($document)
    {
        $resolver = $this->app['fileclient'];

        $arr = array();
        $arr['url'] = $resolver->resolveUrl($document->getRDFFilePath());
        $arr['annotationUrls'] = array();

        foreach ($document->getRDFAnnotationPaths() as $name => $path) {
            $arr['annotationUrls'][$name] = $resolver->resolveUrl($path);
        }

        return $arr;
    }  
}

/* EOF: VocabulariesSingle.php */
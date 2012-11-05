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
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;
use Doctrine\ORM\EntityManager;
use Bioteawebapi\Views\BasicView;

/**
 * Single term info Controller
 */
class TermsSingle extends Abstracts\SingleController
{
    /**
     * @var Bioteawebapi\Entities\Term
     */
    private $termObj = null;

    // --------------------------------------------------------------

    /**
     * Get term based off its name
     *
     * @inherit
     */
    protected function getMainItem($identifier)
    {
        //Get it from the db
        $termObj = array_shift($this->app['dbclient']->getTerms($identifier));

        //Set it to a class property
        $this->termObj = $termObj;

        //Return it
        return $termObj->toArray() ?: false;
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getMainItemName()
    {
        return "Term";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array('/terms/{term}' => "Get information about a single term and its related items");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "Returns information about a single term in HTML";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "Returns information about a single term in JSON";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRelatedItems()
    {
        return array(
            'documents'    => "Documents",
            'topics'       => "Topics",
            'vocabularies' => "Vocabularies"
        );
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

                $topics = $this->termObj->getTopics()->slice($offset, $limit);
                foreach($topics as $topicObj) {

                    $arr = $topicObj->toArray();
                    if ($topicObj->getVocabulary()) {
                        $arr['vocabulary'] = $topicObj->getVocabulary()->toArray();
                    }

                    $items[] = $arr;
                }

            break;
            case 'documents':

                $docs = $this->termObj->getDocuments()->slice($offset, $limit);

                foreach($docs as $docObj) {
                    $items[] = $this->buildDocumentDetails($docObj);
                }

            break;
            case 'vocabularies':

                $vocabs = $this->termObj->getVocabularies()->slice($offset, $limit);

                foreach($vocabs as $vocabObj) {
                    $items[] = $vocabObj->toArray();
                }

            break;
        }

        return $items;
    }

    // --------------------------------------------------------------

    protected function getRelatedItemsCount($which)
    {
        switch ($which) {        
            case 'topics':
                return $this->termObj->getTopics()->count();
            break;
            case 'documents':
                return $this->termObj->getDocuments()->count();
            break;
            case 'vocabularies':
                return $this->termObj->getVocabularies()->count();
            break;
        }
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

/* EOF: TermsSingle.php */
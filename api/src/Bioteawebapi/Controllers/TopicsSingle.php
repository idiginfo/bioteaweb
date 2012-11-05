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
 * Single topic info Controller
 */
class TopicsSingle extends Abstracts\SingleController
{
    /**
     * @var Bioteawebapi\Entities\Term
     */
    private $topicObj = null;

    // --------------------------------------------------------------

    /**
     * Get term based off its name
     *
     * @inherit
     */
    protected function getMainItem($identifier)
    {
        //Get it from the db
        $topicObj = array_shift($this->app['dbclient']->getTopics($identifier));

        //Set it to a class property
        $this->topicObj = $topicObj;

        //Return it
        if ($topicObj) {
            $arr = $topicObj->toArray();

            if ($topicObj->getVocabulary()) {
                $arr['vocabulary'] = $topicObj->getVocabulary()->toArray();
            }

            return $arr;
        }
        else {
            return false;
        }
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getMainItemName()
    {
        return "Topic";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array('/topics/{topic}' => "Get information about a single topic and its related items");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "Returns information about a single topic in HTML";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "Returns information about a single topic in JSON";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRelatedItems()
    {
        return array(
            'documents' => "Documents",
            'terms'    => "Terms"
        );
    }

    // --------------------------------------------------------------

    protected function getRelatedItemsCount($which)
    {
        switch ($which) {
            case 'terms':
                return $this->topicObj->getTerms()->count();
            break;
            case 'documents':
                return $this->topicObj->getDocuments()->count();
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

            case 'terms':

                $terms = $this->topicObj->getTerms()->slice($offset, $limit);
                foreach($terms as $termObj) {
                    $items[] = $termObj->toArray();
                }

            break;  
            case 'documents':

                $docs = $this->topicObj->getDocuments()->slice($offset, $limit);
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

/* EOF: TopicsSingle.php */
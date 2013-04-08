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
class DocumentsSingle extends Abstracts\SingleController
{
    /**
     * @var Bioteawebapi\Entities\Term
     */
    private $docObj = null;

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array(
            '/documents/{document}' => "Get information about a single document and its related items",
            '/articles/{document}'   => "Alias for document/{document}"
        );
    }

    // --------------------------------------------------------------

    protected function getMainItemName()
    {
        return "Document";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "Returns information about a single document in HTML";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "Returns information about a single document in JSON";
    }

    // --------------------------------------------------------------

    protected function getMainItem($identifier)
    {
        if (strtolower(substr($identifier, -4)) != '.rdf') {
            $identifier .= '.rdf';
        }

        //Get it from the db
        $items = $this->app['dbclient']->getDocuments($identifier);
        $docObj = array_shift($items);        

        //Set it to a class property
        $this->docObj = $docObj;

        //Set it to a class property
        return (is_object($docObj)) ? $docObj->toArray() : false;
    }

    // --------------------------------------------------------------

    protected function assignRelatedItems()
    {
        return array(
            'terms'        => 'Terms',
            'topics'       => 'Topics',
            'vocabularies' => 'Vocabularies'
        );
    }

    // --------------------------------------------------------------

    protected function getRelatedItemsCount($which)
    {
        switch ($which) {        
            case 'topics':
                return count($this->docObj->getTopics());
            break;
            case 'terms':
                return count($this->docObj->getTerms());
            break;
            case 'vocabularies':
                return count($this->docObj->getVocabularies());
            break;
        }
    }

    // --------------------------------------------------------------

    protected function getRelatedItems($which, $offset, $limit)
    {
        $items = array();

        switch($which) {
            case 'topics':

                $topics = array_slice($this->docObj->getTopics(), $offset, $limit);
                foreach($topics as $topicObj) {

                    $arr = $topicObj->toArray();
                    if ($topicObj->getVocabulary()) {
                        $arr['vocabulary'] = $topicObj->getVocabulary()->toArray();
                    }

                    $items[] = $arr;
                }

            break;
            case 'terms':

                $terms = array_slice($this->docObj->getTerms(), $offset, $limit);

                foreach($terms as $termObj) {
                    $items[] = $termObj->toArray();
                }

            break;
            case 'vocabularies':

                $vocabs = array_slice($this->docObj->getVocabularies(), $offset, $limit);

                foreach($vocabs as $vocabObj) {
                    $items[] = $vocabObj->toArray();
                }

            break;            
        }

        return $items;        
    }
}

 /* EOF: DocumentsSingle.php */
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
    /** @inherit */
    protected function assignRoutes()
    {
        return array('/documents/{document}' => "Get information about a single document and its related items");
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
        //Get it from the db
        $items = $this->app['dbclient']->getDocuments($identifier);
        $docObj = array_shift($items);        

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

    }

    // --------------------------------------------------------------

    protected function getRelatedItems($which, $offset, $limit)
    {

    }

    // /** @inherit */
    // protected function configure()
    // {
    //     $this->add(new Route('/documents/{document}'));
    //     $this->add(new Format('text/html', 'html', "Returns information about a single document in HTML"));
    //     $this->add(new Format('application/json', 'json', "Returns information about a single document in JSON"));
    // }

    // // --------------------------------------------------------------

    // * @inherit 
    // protected function execute()
    // {
    //     //Get information about a specific term
    //     $documentPath = urldecode($this->getPathSegment(2));
    //     if (substr(strtolower($documentName), -4) != '.rdf') {
    //         $documentPath .= '.rdf';
    //     }

    //     //Document Object
    //     $documentObj = array_shift(
    //         $this->app['dbclient']->getDocuments($documentPath)
    //     );

    //     var_dump($documentObj);
    // }
}

 /* EOF: DocumentsSingle.php */
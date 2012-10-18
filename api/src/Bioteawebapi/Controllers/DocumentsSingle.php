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
class DocumentsSingle extends Abstracts\SingleEntityController
{
    /** @inherit */
    protected function configure()
    {
        $this->add(new Route('/documents/{document}'));
        $this->add(new Format('text/html', 'html', "Returns information about a single document in HTML"));
        $this->add(new Format('application/json', 'json', "Returns information about a single document in JSON"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
        //Get information about a specific term
        $documentPath = urldecode($this->getPathSegment(2));
        if (substr(strtolower($documentName), -4) != '.rdf') {
            $documentPath .= '.rdf';
        }

        //Document Object
        $documentObj = array_shift(
            $this->app['dbclient']->getDocuments($documentPath)
        );

        var_dump($documentObj);
    }
}

 /* EOF: DocumentsSingle.php */
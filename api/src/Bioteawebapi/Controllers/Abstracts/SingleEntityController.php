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

namespace Bioteawebapi\Controllers\Abstracts;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Entities\Document;

/**
 * Single entity controller contains helper methods
 * to make delivering single entity views easier
 */
abstract class SingleEntityController extends Controller
{
    // ------------------------------------------------------------------

    /**
     * Inputs a document class and outputs the URLS for the RDF files
     * 
     * @param Bioteawebapi\Entities\Document
     * @return array
     */
    protected function buildDocumentDetails(Document $document)
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

    // --------------------------------------------------------------

    /** @inherit */
    // abstract protected function configure();

    /** @inherit */
    // abstract protected function execute();
}

/* EOF: SingleEntityController.php */
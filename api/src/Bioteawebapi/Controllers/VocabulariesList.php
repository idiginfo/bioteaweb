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
use Bioteawebapi\Rest\Parameter;

/**
 * Topics list controller
 */
class VocabulariesList extends Abstracts\ListController
{
    /** @inherit */
    protected function configure()
    {
        parent::configure();
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array('/vocabularies' => "Get a list of vocabularies");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "List vocabularies in JSON format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "List vocabularies in HTML format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItemCount()
    {
        return $this->app['dbclient']->count('getVocabularies');
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItems($offset, $limit)
    {
        return $this->app['dbclient']->getVocabularies(null, $offset, $limit);
    }

    // --------------------------------------------------------------

    /**
     * @inherit
     */
    protected function prepareResults(Array $items)
    {
        //Output items
        $outItems = array();

        foreach($items as $item) {
            $outItems[] = array(
                'uri'       => $item->getUri(),
                'shortName' => $item->getShortName()
            );
        }

        return $outItems;
    }    
}

/* EOF: VocabulariesList.php */
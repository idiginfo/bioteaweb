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
 * Terms list controller
 */
class TermsList extends Abstracts\ListController
{
    /** @inherit */
    protected function configure()
    {
        parent::configure();
        $this->add(new Parameter('prefix', '/^[\p{L}\d \-_]+$/i', "An optional prefix to limit query results"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array('/terms' => "Get a list of terms");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "List terms in JSON format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "List terms in HTML format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItemCount()
    {
        $prefix = strtolower($this->getParameter('prefix'));
        return $this->app['dbclient']->count('getTerms', $prefix);
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItems($offset, $limit)
    {
        $prefix = strtolower($this->getParameter('prefix'));
        return $this->app['dbclient']->getTerms($prefix, $offset, $limit);
    }

    // --------------------------------------------------------------

    /**
     * @inherit
     */
    protected function prepareResults(Array $items)
    {
        $outItems = array();

        $includeTopics = (boolean) $this->getParameter('topics');

        foreach ($items as $termObj) {
            $outItems[] = array('term' => $termObj->getTerm());
        }

        return $outItems;
    }
}

/* EOF: TermsList.php */
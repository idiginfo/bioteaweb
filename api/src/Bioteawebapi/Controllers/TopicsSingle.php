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
        return $topicObj->toArray() ?: false;
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
            'vocabularies' => "Vocabularies",
            'terms'    => "Terms"
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
        $viewParams = array();

        //@TODO: Implement this.  Copy from terms
        return $viewParams;
    }
}

/* EOF: TopicsSingle.php */
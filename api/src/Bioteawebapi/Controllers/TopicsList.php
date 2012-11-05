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
class TopicsList extends Abstracts\ListController
{
    /** @inherit */
    protected function configure()
    {
        parent::configure();
        $this->add(new Parameter('terms', array(0, 1), "An optional prefix to include terms associated with this topic"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignRoutes()
    {
        return array('/topics' => "Get a list of topics");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "List topics in JSON format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "List topics in HTML format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItemCount()
    {
        return $this->app['dbclient']->count('getTopics');
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItems($offset, $limit)
    {
        return $this->app['dbclient']->getTopics(null, $offset, $limit);
    }

    // --------------------------------------------------------------

    /**
     * @inherit
     */
    protected function prepareResults(Array $items)
    {
        $outItems = array();

        $includeTerms = (boolean) $this->getParameter('terms');

        foreach ($items as $topicObj) {

            //Topic Array
            $outArr = array(
                'id'        => $topicObj->getId(),
                'uri'       => $topicObj->getUri(),
                'shortName' => $topicObj->getShortName()
            );

            if ($includeTerms) {

                foreach($topicObj->getTerms() as $term) {
                    $outArr['terms'][] = $term->getTerm();
                }
            }


            $outItems[] = $outArr;
        }

        return $outItems;
    } 
}

/* EOF: TopicsList.php */
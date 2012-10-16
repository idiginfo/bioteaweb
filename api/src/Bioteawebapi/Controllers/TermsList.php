<?php

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
        $this->add(new Parameter('topics', array(0, 1), "An optional prefix to include topic information about each term"));
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
            $outArr = array('term' => $termObj->getTerm());

            if ($includeTopics) {

                foreach($termObj->getTopics() as $topic) {
                    $outArr['topics'][] = array(
                        'uri' => $topic->getUri(),
                        'shortName' => $topic->getShortName()
                    );
                }
            }


            $outItems[] = $outArr;
        }

        return $outItems;
    }
}

/* EOF: TermsList.php */
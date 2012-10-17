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
class TermsSingle extends Controller
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    // --------------------------------------------------------------

    /** @inherit */
    protected function configure()
    {
        $this->add(new Route('/terms/{term}'));
        $this->add(new Format('text/html', 'html', "Returns information about a single term in HTML"));
        $this->add(new Format('application/json', 'json', "Returns information about a single term in JSON"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
        //Get information about a specific term
        $termStr = urldecode($this->getPathSegment(2));
        $termObj = array_shift($this->app['dbclient']->getTerms($termStr));

        //View Parameters
        $viewParams = array();
        $viewParams['term'] = $termObj->toArray();

        //Include topics and vocabularies
        foreach($termObj->getTopics() as $topicObj) {
            $arr = $topicObj->toArray();
            if ($topicObj->getVocabulary()) {
                $arr['vocabulary'] = $topicObj->getVocabulary()->toArray();
            }
            $viewParams['topics'][] = $arr;
        }

        //Include documents related to this term
        foreach($termObj->getAnnotations() as $annotObj) {
            $docArr = $annotObj->getDocument()->toArray();

            if ( ! isset($viewParams['documents']) OR ! in_array($docArr, $viewParams['documents'])) {
                $viewParams['documents'][] = $docArr;
            }
        }

        $output = new BasicView($viewParams);

        //Output it!
        switch($this->format) {
            case 'text/html':
                return $output->toHtml();
            case 'application/json':
                return $output->toJson();
        }        
    }
}

/* EOF: TermsSingle.php */
<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\BasicView;

/**
 * Front Controller
 */
class Front extends Controller
{
    // --------------------------------------------------------------
   
    protected function configure()
    {
        $this->add(new Route('/', null, "Get information about the API"));
        $this->add(new Route('/stats', null, "Get counts of indexed items"));
        $this->add(new Format('text/html', 'html', "HTML page showing information about the API"));
        $this->add(new Format('application/json', 'json', "JSON document containing information about the API"));
    }

    // --------------------------------------------------------------

    protected function execute()
    {
        switch($this->getPathSegment(1)) {
            case 'stats':
                return $this->getStats();
            default:
                return $this->getFrontPage();
        }
    }

    // --------------------------------------------------------------

    protected function getFrontPage()
    {
        switch($this->format) {

            case 'application/json':
                return $this->app->json($this->getSummary());
            case 'text/html': default:
                return $this->app->json($this->getSummary());
            break;
        }
    }

    // --------------------------------------------------------------

    protected function getStats()
    {   
        //Get stats
        $stats = new BasicView();
        $stats->counts = array(
            'documents'    => (int) $this->app['dbclient']->count('getDocuments'),
            'terms'        => (int) $this->app['dbclient']->count('getTerms'),
            'topics'       => (int) $this->app['dbclient']->count('getTopics'),
            'vocabularies' => (int) $this->app['dbclient']->count('getVocabularies')
        );

        switch($this->format) {

            case 'application/json':
                return $stats->toJson();
            case 'text/html': default:
                return $stats->toHtml();
            break;
        }        
    }
}

/* EOF: Front.php */
<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;

/**
 * Front Controller
 */
class Front extends Controller
{
    protected function configure()
    {
        $this->addRoute('/');
        $this->addFormat('text/html', 'html', "HTML page showing information about the API");
        $this->addFormat('application/json', 'json', "JSON document containing information about the API");
    }

    protected function execute()
    {
        switch($this->format) {

            case 'application/json':
                return $this->app->json($this->getSummary());
            case 'text/html': default:
                return 'HAI';
            break;
        }
    }

}

/* EOF: Front.php */
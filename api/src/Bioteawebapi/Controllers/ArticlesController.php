<?php

namespace Bioteawebapi\Controllers;

use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Format;

class ArticlesController extends Controller
{
    /**
     * Configure method is called when the controller is instantiated
     * 
     * {inherit}
     */
    protected function configure()
    {
        $this->add(new Route('/articles', 'get'));
        $this->add(new Route('/articles/{article}', 'get'));

        //Necessary for the API, but not necessary for anything else
        $this->add(new Format('application/json', 'json', "Redirect"));

    }

    // --------------------------------------------------------------

    /**
     * Execute method is called when the controller is run
     */
    protected function execute()
    {
        return $this->app->redirect('documents');
    }
}

/* EOF: ArticlesController.php */
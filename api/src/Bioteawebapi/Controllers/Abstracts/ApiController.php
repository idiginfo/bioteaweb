<?php

namespace Bioteawebapi\Controllers\Abstracts;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;

/**
 * Biotea API Controller for shared functionality
 */
abstract class ApiController extends Controller
{
    protected function configure()
    {
        //Routes
        foreach ($this->assignRoutes() as $route => $desc) {
            $this->add(new Route($route, 'get', $desc));
        }

        //Format descriptions
        $this->add(new Format('text/html', 'html', $this->assignHtmlDesc()));
        $this->add(new Format('application/json', 'json', $this->assignJsonDesc()));        
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
        $output = $this->getOutput();

        //Get the summary
        $summary = $this->getSummary();

        //Set the HTML content to show both the data and the summary
        $htmlContent = sprintf(
            "<h3>Data</h3><pre class='json'>%s</pre><h3>Documentation</h3><pre class='json'>%s</pre>",
            $output->toJson(), json_encode($summary)
        );

        //JSON Content should include the data and the summary
        $jsonContent = json_encode(array(
            'data'          => $output->toArray(),
            'documentation' => $summary
        ));

        //Output it!
        switch($this->format) {
            case 'text/html':
                return $output->toHtml($htmlContent);
            case 'application/json':
                return $jsonContent;
        }
    }

    // --------------------------------------------------------------

    /** @return array  Array of routes as keys and descriptions as values */
    protected abstract function assignRoutes();

    /** @return string */
    protected abstract function assignHtmlDesc();

    /** @return string */
    protected abstract function assignJsonDesc();    

    /** @return Bioteawebapi\Rest\View */
    protected abstract function getOutput();
}

/* EOF: ApiController.php */
<?php

namespace Bioteawebapi\Controllers\Abstracts;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;

abstract class ListController extends Controller
{
    /**
     * @var int  Hardcoded items per page - - Can be abstracted
     */
    private $itemsPerPage = 100;

    // --------------------------------------------------------------

    protected function configure()
    {
        foreach ($this->assignRoutes() as $route => $desc) {
            $this->add(new Route($route, 'get', $desc));
        }

        $this->add(new Format('text/html', 'html', $this->assignHtmlDesc()));
        $this->add(new Format('application/json', 'json', $this->assignJsonDesc()));
        $this->add(new Parameter('page', '/^[\d]+$/', "Which page to retrieve for records that span multiple pages"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
        //Call to itemCount
        $itemCount = $this->getItemCount();

        $page   = $this->getParameter('page') ?: 1;
        $offset = ($page == 1) ? 0 : ($page - 1) * $this->itemsPerPage;
        $limit  = $this->itemsPerPage;

        //Call to getItems
        $items = $this->getItems($offset, $limit);
        $items = $this->prepareResults($items);


        //Setup output view
        $output = new PaginatedList($itemCount, $this->itemsPerPage);
        $output->setItems($items);
        $output->setOffset($offset);

        //Output it!
        switch($this->format) {
            case 'text/html':
                return $output->toHtml();
            case 'application/json':
                return $output->toJson();
        }
    }

    // --------------------------------------------------------------

    /** @return array  Array of routes as keys and descriptions as values */
    protected abstract function assignRoutes();

    /** @return string */
    protected abstract function assignHtmlDesc();

    /** @return string */
    protected abstract function assignJsonDesc();

    /** @return int */
    protected abstract function getItemCount();

    /** @return array */
    protected abstract function getItems($offset, $limit);

    // --------------------------------------------------------------

    /**
     * Modify items performs any modifications to the items before returning
     * 
     * @param array $items
     * @return array
     */ 
    protected function prepareResults(Array $items)
    {
        //This doesn't do anything unless overridden
        return $items;
    }
}

/* EOF: ListController.php */
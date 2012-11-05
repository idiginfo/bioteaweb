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

namespace Bioteawebapi\Controllers\Abstracts;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;

/**
 * Single Controller is for getting information about a single
 * item and optionally its dependencies
 */
abstract class SingleController extends ApiController
{
    /**
     * @var int  Hardcoded items per page
     */
    private $itemsPerPage = 100;

    // --------------------------------------------------------------

    protected function configure()
    {
        parent::configure();

        //Related items
        $ritems = $this->assignRelatedItems();

        if (count($ritems) > 0) {
            $this->add(new Parameter('page', '/^[\d]+$/', "Which page to retrieve for lists of related items that span multiple pages", 1));
            $this->add(new Parameter('related', array_keys($ritems), "Set to also retrieve related " . implode(" or ", $ritems)));
        }        
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getOutput()
    {
        $identifier = urldecode($this->getPathSegment(2));
        $item = $this->getMainItem($identifier);

        //404 if not found
        if ( ! $item) {
            $this->app->abort(404, $this->getMainItemName() . " Not Found");
        }

        //Build a view for the actual item
        $output = $this->app['viewfactory']->build('EntityView', array($item));

        //Get related item view
        if ($this->getParameter('related')) {
            $related   = $this->getParameter('related');
            $itemCount = $this->getRelatedItemsCount($related);
            $page      = $this->getParameter('page') ?: 1;
            $offset    = ($page == 1) ? 0 : ($page - 1) * $this->itemsPerPage;
            $limit     = $this->itemsPerPage;
            $items     = $this->getRelatedItems($related, $offset, $limit);

            //Build a view object
            $relatedItems = $this->app['viewfactory']->build('PaginatedList', array($itemCount, $this->itemsPerPage));
            $relatedItems->setItems($items);
            $relatedItems->setOffset($offset);

            //Add the related items view to the output
            $output->setRelatedItems($relatedItems, $this->getParameter('related'));
        }

        return $output;
    }

    // --------------------------------------------------------------

    /** @return string  A human-friendly name for the item being viewed */
    protected abstract function getMainItemName();

    /**
     * @param string       An identifier for the item to get
     * @return array|null  NULL if it does not exist 
     */
    protected abstract function getMainItem($identifier);

    // --------------------------------------------------------------

    /**
     * Assign related items options
     *
     * @return array  Keys are names, values are human-friendly names
     */
    protected function assignRelatedItems()
    {
        return array();
    }

    // --------------------------------------------------------------

    /**
     * @return int 
     */
    protected function getRelatedItemsCount($which)
    {
        return null;
    }

    // --------------------------------------------------------------

    /**
     * @return array 
     */
    protected function getRelatedItems($which, $offset, $limit)
    {
        return array();
    }
}

/* EOF: SingleController.php */
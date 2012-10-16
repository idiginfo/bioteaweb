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

namespace Bioteawebapi\Views;
use Bioteawebapi\Rest\View;

/**
 * Basic view is a thin array wrapper
 */
class BasicView extends View
{
    private $items;

    // --------------------------------------------------------------

    public function __construct(Array $params = array())
    {
        foreach($params as $k => $v) {
            $this->addItem($k, $v);
        }
    }

    // --------------------------------------------------------------

    public function __set($item, $val)
    {
        $this->addItem($item, $val);
    }

    // --------------------------------------------------------------

    public function __get($item)
    {
        return $this->getItem($item);
    }

    // --------------------------------------------------------------

    /**
     * Get an array of the items
     */
    public function toArray()
    {
        return $this->items;
    }


    // --------------------------------------------------------------

    /**
     * Set an item
     *
     * @param string $item
     * @param mixed $val
     */
    public function addItem($item, $val)
    {
        $this->items[$item] = $val;
    }

    // --------------------------------------------------------------

    /**
     * Get an item
     *
     * @return mixed
     */
    public function getItem($item)
    {
        return $this->items[$item];
    }
}

/* EOF: BasicView.php */
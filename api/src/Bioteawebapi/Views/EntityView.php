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
 * Entity view is for viewing information about
 * an entity or its related items
 */
class EntityView extends BasicView
{
    /**
     * @var PaginatedList
     */
    private $relatedItems = null;

    /**
     * @var string
     */
    private $relatedItemsPropertyName;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array $entityInfo  Optionally preload entity info
     */
    public function __construct(Array $entityInfo = array())
    {
        foreach($entityInfo as $k => $v) {
            $this->__set($k, $v);
        }
    }

    // --------------------------------------------------------------

    /**
     * Set Related Items
     *
     * @param PaginatedList $relatedItems
     * @param string        $propertyName
     */
    public function setRelatedItems(PaginatedList $relatedItems, $propertyName = 'related')
    {
        $this->relatedItemsPropertyName = $propertyName;
        $this->relatedItems = $relatedItems;
    }

    // --------------------------------------------------------------

    /**
     * Get Magic Method
     *
     * Overrides default public access behavior
     *
     * @param string $item
     * @return mixed
     */
    public function __get($item)
    {
        $arr = parent::toArray();
        return (isset($arr[$item])) ? $arr[$item] : null;
    }

    // --------------------------------------------------------------

    /**
     * View as array
     *
     * Overrides the default magic method
     *
     * @return array
     */
    public function toArray()
    {
        //Get the basic entity info
        $arr = parent::toArray();

        //Add the related items if there are are any
        if ($this->relatedItems) {

            $ritemsArr = $this->relatedItems->toArray();

            //Rename some properties for clarity
            $arr[$this->relatedItemsPropertyName]                    = $ritemsArr['items'];
            $arr['num' . ucfirst($this->relatedItemsPropertyName)]   = $ritemsArr['numItems'];
            $arr['first' . ucfirst($this->relatedItemsPropertyName)] = $ritemsArr['firstItem'];
            $arr['last' . ucfirst($this->relatedItemsPropertyName)]  = $ritemsArr['lastItem'];

            //Unset the old ones
            unset(
                $ritemsArr['items'],
                $ritemsArr['numItems'],
                $ritemsArr['firstItem'],
                $ritemsArr['firstItem']
            );

            $arr = array_merge($arr, $ritemsArr);
        }

        return $arr;
    }    
}

/* EOF: EntityView.php */
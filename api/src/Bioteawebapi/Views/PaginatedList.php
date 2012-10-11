<?php

namespace Bioteawebapi\Views;
use Bioteawebapi\Rest\View;

class PaginatedList extends View
{
    /**
     * @var int  Total number of pages
     */
    private $numPages;

    /**
     * @var int  Total number of items
     */ 
    private $numItems;

    /**
     * @var int  Number per page
     */
    private $numPerPage;

    /**
     * @var array  Array of items
     */
    private $items;

    /**
     * @var int  The current page
     */
    private $page;

    /**
     * @var int  The start item number on the current page
     */
    private $first;

    /**
     * @var int  The end item number on the current page
     */
    private $last;

    // --------------------------------------------------------------

    public function __construct($numItems, $numPerPage)
    {
        $this->numItems = (int) $numItems;
        $this->numPerPage = (int) $numPerPage;
        $this->first = 1; //assume no offset to start
    }

    // --------------------------------------------------------------

    /**
     * Set items
     *
     * Also does all of the calcuations
     *
     * @param array $items  An incremental array of items (keys are ignored)
     * @param int $offset   The offset
     */
    public function setItems(Array $items, $offset = null)
    {
        //Set all the records
        array_map(array($this, 'addItem'),  array_values($items));

        //Set the offset if it is not already set or if it is explictely defined
        if ( ! is_null($offset)) {
            $this->setOffset((int) $offset);
        }
    }

    // --------------------------------------------------------------

    /**
     * Set the Offset
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->first = $offset + 1;
        $this->calculateInfo();
    }

    // --------------------------------------------------------------

    /**
     * Add Item
     *
     * @param mixed $item
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        $this->calculateInfo();
    }

    // --------------------------------------------------------------

    /**
     * Calculate all of the info
     */
    private function calculateInfo()
    {
        //What we know is first, batch size, and total record size
        $this->last = ($this->first - 1) + $this->numPerPage;

        //Fix last if on last page
        if ($this->last > $this->numItems) {
            $this->last = $this->numItems;
        }

        //Figre out page info
        $this->numPages = ceil($this->numItems / $this->numPerPage);
        $this->page     = floor($this->first / $this->numPerPage) + 1;
    }

}

/* EOF: PaginatedList.php */
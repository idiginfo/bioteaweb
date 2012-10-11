<?php

namespace Bioteawebapi\Views;

class PaginatedListTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateAsObjectSucceeds()
    {
        $obj = new PaginatedList(100);
        $this->assertInstanceOf("\Bioteawebapi\Views\PaginatedList", $obj);
    }

    // --------------------------------------------------------------
    
    /**
     * @dataProvider testCalculateVariablesProvider
     *
     * @param int $numPages
     * @param int $itemsPerPage
     * @param int $offset
     * @param array $expected  (0 = numPages, 1 = first, 2 = last, 3 = currPage)
     */
    public function testCalculateVariables($numItems, $itemsPerPage, $offset, $expected)
    {
        $obj = new PaginatedList($numItems);
        $obj->setItems(range($offset, ($offset-1) + $itemsPerPage));
        $obj->setOffset($offset);

        $this->assertEquals($expected[0], $obj->numPages);
        $this->assertEquals($expected[1], $obj->first);
        $this->assertEquals($expected[2], $obj->last);
        $this->assertEquals($expected[3], $obj->page);
    }

    // --------------------------------------------------------------

    /**
     * Provider for testCalulateVariables
     */
    public function testCalculateVariablesProvider()
    {
        $out = array();
        $out[] = array(100, 20, 0, array(5, 1, 20, 1));
        $out[] = array(100, 20, 39, array(5, 40, 59, 3));
        return $out;
    }
}

/* EOF: PaginatedList.php */
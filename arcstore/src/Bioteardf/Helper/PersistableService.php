<?php

namespace Bioteardf\Helper;

interface PersistableService
{
    /**
     * Check if database schema is setup
     *
     * @return boolean  TRUE if up-to-date
     */
    function isSetUp();

    // --------------------------------------------------------------    

    /**
     * Setup the database tables
     *
     * @return int  The number of queries run
     */
    function setUp();

    // --------------------------------------------------------------    

    /**
     * Drop all data and tables for this resource
     *
     * @return boolean  TRUE if cleared, FALSE otherwise
     */
    function reset();
}

/* EOF: PersistableService.php */
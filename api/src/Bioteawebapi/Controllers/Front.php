<?php

namespace Bioteawebapi\Controllers;

/**
 * Front Controller
 */
class Front extends Controller
{
    // --------------------------------------------------------------

    /**
     * Run the front controller
     */
    public function run()
    {
        //For now, we'll just be a-testin
        return $this->test();
    }


    // --------------------------------------------------------------

    /** @inheritdoc */
    public function getAllowedFormats()
    {
        return array('json', 'html');
    }

    // --------------------------------------------------------------

    /**
     * Test method - delete me
     */
    public function test()
    {
        echo "Oh Hai";
    }

}

/* EOF: Front.php */
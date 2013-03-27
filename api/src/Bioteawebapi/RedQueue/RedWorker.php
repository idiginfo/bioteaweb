<?php

namespace Bioteawebapi\RedQueue;

class RedWorker
{
    
    
    // --------------------------------------------------------------

    public function __construct($queues = array())
    {
        //Queues should be an array
        if ( ! is_array($queues)) {
            $queues = array( (string) $queues);
        }
    }

    // --------------------------------------------------------------

    protected function work()
    {

    }    
}

/* EOF: RedWorker.php */
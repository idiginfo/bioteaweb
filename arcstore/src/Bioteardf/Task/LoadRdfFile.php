<?php

namespace Bioteardf\Task;

use Minions\TaskHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BioteaRdf\Service\RdfLoader;

class LoadRdfFile implements TaskHandlerInterface
{  
    /**
     * @var BioteaRdf\Service\RdfLoader $loader
     */
    var $loader;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param BioteaRdf\Service\RdfLoader $loader
     */
    public function __construct(RdfLoader $loader)
    {
        $this->loader = $loader;
    }

    // --------------------------------------------------------------

    /**
     * Perform the task
     *
     * @param object $data
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @return int  The number of triples loaded
     */
    public function run($data, EventDispatcherInterface $dispatcher = null)
    {
        //Get the filename from the data
        $filename = $data->filename;

        //Check it
        if ( ! is_readable($filename)) {
            throw new RuntimeException("Could not read from the file: " . $filename);
        }

        //Load it
        return $this->loader->loadFile($filename);
    }

}

/* EOF: LoadRdfFile.php */
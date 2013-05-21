<?php

namespace Bioteardf\Task;

use Bioteardf\Helper\RdfSetTask;
use Minions\TaskHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Bioteardf\Service\TripleStore\RdfLoader;
use Bioteardf\Model\BioteaRdfSet;
/**
 * Very simple wrapper class for loading RDF Sets
 *
 * This particular class only happens to be used when run asynchronously
 */
class LoadRdfSet extends RdfSetTask implements TaskHandlerInterface
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
     * @param BioteaRdfSet|string $data  BioteaRdfSet object or serialized data
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @return int  The number of triples loaded
     */
    public function run($data, EventDispatcherInterface $dispatcher = null)
    {
        $set = $this->deserializeRdfSet($data[0]);
        return $this->loader->loadFileSet($set);
    }
}

/* EOF: LoadRdfFile.php */
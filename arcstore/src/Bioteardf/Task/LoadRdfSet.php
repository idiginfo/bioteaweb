<?php

namespace Bioteardf\Task;

use Minions\TaskHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Bioteardf\Service\RdfLoader;
use Bioteardf\Model\BioteaRdfSet;
use RuntimeException;
use SplFileInfo;

/**
 * Very simple wrapper class for loading RDF Sets
 *
 * This particular class only happens to be used when run asynchronously
 */
class LoadRdfSet implements TaskHandlerInterface
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

    // --------------------------------------------------------------

    private function deserializeRdfSet($jsonData)
    {
        $data = json_decode($jsonData);
        $mainFile   = new SplFileInfo($data->mainFile);
        $annotFiles = array_map(
            function($fp) {
                return new SplFileInfo($fp);
            },
            $data->annotationFiles
        );

        return new BioteaRdfSet($mainFile, $annotFiles);
    }
}

/* EOF: LoadRdfFile.php */
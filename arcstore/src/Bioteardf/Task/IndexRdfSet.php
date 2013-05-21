<?php

namespace Bioteardf\Task;

use Minions\TaskHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Bioteardf\Helper\RdfSetTask;
use Bioteardf\Model\BioteaRdfSet;
use Bioteardf\Service\RdfSetTracker;
use Bioteardf\Service\Indexes\BioteaRdfSetParser;
use Bioteardf\Service\Indexes\DocObjectPersister;
use Bioteardf\Exception\BioteaRdfParseException;


/**
 * Very simple wrapper class for loading RDF Sets
 *
 * This particular class only happens to be used when run asynchronously
 */
class IndexRdfSet extends RdfSetTask implements TaskHandlerInterface
{  
    const SKIP    = -1;
    const FAIL    = 0;
    const SUCCESS = 1;

    // --------------------------------------------------------------

    /**
     * @var Bioteardf\Service\RdfSetTracker 
     */
    private $tracker;

    /**
     * @var Bioteardf\Service\Indexes\BioteaRdfSetParser
     */
    private $parser;

    /**
     * @var Bioteardf\Service\Indexes\DocObjectPersister
     */
    private $persister;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Bioteardf\Service\RdfSetTracker               $tracker
     * @param Bioteardf\Service\Indexes\BioteaRdfSetParser  $parser
     * @param Bioteardf\Service\Indexes\DocObjectPersister  $persister
     */
    public function __construct(RdfSetTracker $tracker, BioteaRdfSetParser $parser, DocObjectPersister $persister)
    {
        //Ensure Garbage collection enabled
        gc_enable();

        $this->tracker   = $tracker;
        $this->parser    = $parser;
        $this->persister = $persister;
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
        //Deserialize
        $rdfSet = $this->deserializeRdfSet($data['rdfSet']);
        $force  = (boolean) $data['force'];

        //Check for existing document
        if ( ! $force && $this->tracker->actionAlreadyPerformed($rdfSet, 'indexed')) {
            return self::SKIP;
        }

        //Parse and persist
        try {

            //Parse
            $objGraph = $this->parser->analyzeSet($rdfSet);

            //Persist
            $this->persister->persist($objGraph);

            //Record
            $this->tracker->recordAction($rdfSet, 'indexed');

            //Cleanup memory
            $this->persister->cleanup();

            unset($objGraph);
            gc_collect_cycles();

            //Return
            return self::SUCCESS;
        } 
        catch (BioteaRdfParseException $e) {
            return self::FAIL;
        }
        
    }
}

/* EOF: IndexRdfSet.php */
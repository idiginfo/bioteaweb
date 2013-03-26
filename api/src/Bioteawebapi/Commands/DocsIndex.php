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

namespace Bioteawebapi\Commands;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use TaskTracker\Tracker;
use TaskTracker\OutputHandler\SymfonyConsole as TrackerConsoleHandler;
use TaskTracker\OutputHandler\Monolog as TrackerMonologHandler;

/**
 * Recursively index RDF file and report output to logs and to the console
 */
class DocsIndex extends Command implements EventSubscriberInterface
{
    const NO_LIMIT = 0;

    // --------------------------------------------------------------

    /**
     * @var TaskTracker\Tracker
     */
    private $tracker;

    // --------------------------------------------------------------

    /**
     * Returns an array of event names this subscriber wants to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            'indexer.startall'  => 'startIndexListener',
            'indexer.finishall' => 'finishIndexListener',
            'indexer.after'     => 'afterIndexListener',
            'indexer.fail'      => 'failIndexListener'
        );
    }

    // --------------------------------------------------------------

    public function startIndexListener(GenericEvent $event)
    {
        if ($this->tracker) {
            $this->tracker->start();
        }
    }

    // --------------------------------------------------------------

    public function finishIndexListener(GenericEvent $event)
    {
        if ($this->tracker) {
            $this->tracker->finish(
                sprintf("Finished indexing %s documents.", number_format($event->getArgument('processed')), 0)
            );
        }
    }

    // --------------------------------------------------------------

    public function afterIndexListener(GenericEvent $event)
    {
        //Event::subject is either 'success' or 'skip' to the TaskTracker
        if ($this->tracker) {
            $this->tracker->tick("Processing", $event->getSubject());
        }
    }

    // --------------------------------------------------------------

    public function failIndexListener(GenericEvent $event)
    {
        //false is 'fail' to the TaskTracker
        if ($this->tracker) {
            $this->tracker->tick("Processing", (int) false);
        }

    }

    // --------------------------------------------------------------

    protected function configure()
    {
        //Setup command stuff
        $this->setName('docs:index')->setDescription('Index documents into MySQL (and SOLR, if app is configuerd to do so)');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Folder path to index. If not specified, path in config is used');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit number of indexed documents (excluding skipped)');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Setup event listeners
        $this->app['dispatcher']->addSubscriber($this);
        $this->app['indexer']->setEventDispatcher($this->app['dispatcher']);

        //Check db
        if ( ! $this->app['dbclient']->checkSchema()) {
            throw new \RuntimeException("Schema not up-to-date.  Run schema:build to correct this.");
        }

        //Set limit
        $limit = (int) $input->getOption('limit') ?: self::NO_LIMIT;

        //Get the path if it is specified
        if ($input->getArgument('path')) {
            $this->app['fileclient']->setBasePath($input->getArgument('path'));
        }

        //Output to log
        $trackerHandlers = array(
            new TrackerMonologHandler($this->app['monolog'], 60),
            new TrackerConsoleHandler($output)            
        );

        //Setup a task tracker
        $this->tracker = new Tracker($trackerHandlers, $limit ?: Tracker::UNKNOWN);

        //Turn error reporting off..
        error_reporting(0);

        //Add the task tracker and run the indexer
        $this->app['indexer']->indexAll($limit);
    }
}
/* EOF: Index.php */
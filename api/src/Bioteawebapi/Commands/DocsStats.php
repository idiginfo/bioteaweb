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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bioteawebapi\Entities\Document;

use TaskTracker\Tracker, TaskTracker\Tick;
use TaskTracker\OutputHandler\SymfonyConsole as TrackerConsoleHandler;

/**
 * Get some statistics from the documents
 */
class DocsStats extends Command
{
    private $stats = array();

    /**
     * @var \MongoDb
     */
    private $mongo;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('docs:stats')->setDescription('Get some statistics from the documents');
        $this->addArgument('mode', InputArgument::REQUIRED, "Either 'w'/'worker' for worker, 'm'/'main' for main process, or 'g'/'get'");

        //General options
        $this->addOption('gearman', 'g', InputOption::VALUE_REQUIRED, 'Gearman server (default is localhost)', 'localhost');
        $this->addOption('mongo',   'm', InputOption::VALUE_REQUIRED, 'Mongo Connection String', 'mongodb://localhost:27017');

        //Options for main task
        $this->addOption('limit',   'l', InputOption::VALUE_REQUIRED,  'Limit number of documents analyzed', 0);
        $this->addOption('triples', 't', InputOption::VALUE_NONE,      "Include triples (adds a bunch of time)");

        //Options for 'get'
        $this->addOption('outdir', 'o', InputOption::VALUE_REQUIRED, 'Output directory for \'get\' mode', '.');
    }


    // --------------------------------------------------------------

    /** @inherit */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Get mode
        $mode = $input->getArgument('mode');

        //Options check
        if ($mode{0} != 'm') {
            if ($input->getOption('limit') != 0) {
                throw new \InvalidArgumentException("Limit can only be set for non-worker mode");
            }
            if ($input->getOption('triples') != null) {
                throw new \InvalidArgumentException("Triples can only be set for non-worker mode");
            }
        }
        if ($mode{0} != 'g') {
            if ($input->getOption('outdir') != '.') {
                throw new \InvalidArgumentException("Outdir can only be set for the 'get' mode");
            }
        }

        //check for gearman and Mongo
        if ( ! class_exists('\GearmanWorker')) {
            throw new \Exception("Missing PECL dependency: gearman");
        }
        if ( ! class_exists('\Mongo')) {
            throw new \Exception("Mssing PECL dependency: mongo");
        }

        //Setup Mongo
        $mongo = new \Mongo($input->getOption('mongo'));
        $this->mongo = $mongo->bioteawebstats;

        //If worker option 
        switch ($mode{0}) {
            case 'w':
                return $this->doWorker($input, $output);
            case 'm':
                return $this->doMain($input, $output);
            case 'g':
                return $this->doGet($input, $output);
            default:
                throw new \InvalidArgumentException("Mode must be 'worker' or 'main' (w or m)");
        }
    }

    // --------------------------------------------------------------

    /**
     * Do Main Script
     */
    protected function doMain(InputInterface $input, OutputInterface $output)
    {
        //Limit
        $limit = $input->getOption('limit');

        //If not non-interactive, prompt "are you sure?"
        if ($this->mongo->articles->count() > 0 && ! $input->getOption('no-interaction')) {

            $dialog = $this->getHelperSet()->get('dialog');
            if ( ! $dialog->askConfirmation($output,
                    '<error>WARNING: This will DESTROY all data.</error> Are you sure you want to do this? [y/n]: ', false
                )) {
                return;
            }
        }

        //Start time is here
        $startTime = microtime(true);

        //Drop Mongo Database
        $this->mongo->drop();

        //Gearman client
        $gearman = new \GearmanClient();
        $gearman->addServers($input->getOption('gearman'));

        //Count
        $count = 0;

        //Send all the files to gearman
        $tracker = new Tracker(new TrackerConsoleHandler($output), $limit ?: Tracker::UNKNOWN);
        $tracker->start("Sending files to Gearman workers...");
        while ($docPath = $this->app['fileclient']->getNextFile()) {

            $msg = sprintf("Processing");

            //Get out if past limit
            if ($limit && $count >= $limit) {
                break;
            }

            //Create a worker task for the queue
            $data = array('docPath' => $docPath, 'doTriples' => (boolean) $input->getOption('triples'));
            $gearman->doBackground('processFile', json_encode($data), md5($docPath));

            //Increment
            $tracker->tick($msg);
            $count++;
        
        }

        $tracker->finish();

        //Build a new tracker
        $output->writeln("\nBackground Jobs Processing - Counting Records...\n");

        //Wait until all documents are in the database to finish up...
        $numInDb = 0;
        $ac = $this->mongo->articles;
        while($numInDb < $count) {

            sleep(1);

            $numFailed  = $ac->count(array('status' => 'fail'));
            $numSucceed = $ac->count(array('status' => 'succeed'));
            $numInDb    = $ac->count();
            $now        = microtime(true);

            $msg = sprintf(
                "Counting... [%s of %s] | %s succeed | %s failed | %s elapsed\r", 
                number_format($numInDb),
                number_format($count),
                number_format($numSucceed),
                number_format($numFailed),
                number_format($now - $startTime, 2)
            );

            $output->writeln($msg);
        }

        $output->writeln("All Done.");
    }

    // --------------------------------------------------------------

    protected function doGet(InputInterface $input, OutputInterface $output)
    {
        //If no data, return error.
        if ($this->mongo->articles->count() == 0) {
            $output->writeln("<error>No data yet.  Have you run the stats script?</error>");
            return;
        }

        //Outdir
        $outdir = realpath($input->getOption('outdir'));

        //If output dir is not writable, return error
        if ( ! is_writable($outdir)) {
            throw new \RuntimeException("Ouput directory not writable: " . $outdir);
        }


        //Journal CSV
        $journalFileName = $outdir . DIRECTORY_SEPARATOR . 'biotea_journals_' . time() . '.csv';
        $journalFH = fopen($journalFileName, 'w');
        fputcsv($journalFH, array('Journal', '# Articles'));
        foreach($this->mongo->journals->find() as $row) {
            fputcsv($journalFH, array($row['journal'], $row['articles']));
        }
        fclose($journalFH);

        //Vocab CSV
        $vocabFileName = $outdir . DIRECTORY_SEPARATOR . 'biotea_vocabs_' . time() . '.csv';
        $vocabFH = fopen($vocabFileName, 'w');
        fputcsv($vocabFH, array('Vocabulary', '# Docs', '# Terms', '# Topics'));
        foreach($this->mongo->listCollections() as $coll) {
            if (substr($coll->getName(), 0, strlen('vocab_')) == 'vocab_') {
                
                $items = array();
                $vocabName = substr($coll->getName(), strlen('vocab_'));

                //Vocab name
                $items[0] = $vocabName;

                //Num docs
                $row = $this->mongo->vocabdoccounts->findOne(array('vocabulary' => $vocabName));
                $items[1] = $row['numdocs'];

                //Num terms
                $numterms = $coll->find(array('type' => 'term'))->count();
                $items[2] = $numterms;

                //Num topics
                $numtopics = $coll->find(array('type' => 'topic'))->count();
                $items[3] = $numtopics;

                fputcsv($vocabFH, $items);
            }
        }
        fclose($vocabFH);
    }

    // --------------------------------------------------------------

    /**
     * Do Gearman Worker (for worker scripts)
     */
    protected function doWorker(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Starting worker task... PID: " . getmypid());

        $worker = new \GearmanWorker();
        $worker->addServers($input->getOption('gearman'));
        $worker->addFunction('processFile', array($this, 'processFile'));

        //Infinite loop 
        while ($worker->work()) {

            if ($worker->returnCode() != \GEARMAN_SUCCESS) {
                $output->writeln("Error: " . $worker->error());
            }
        }
    }

    // --------------------------------------------------------------

    /**
     * Process a file from a Gearman job
     *
     * @param \GearmanJob $job
     */
    public function processFile(\GearmanJob $job)
    {
        //Workload data
        $data = json_decode($job->workload(), true);
        $docPath   = $data['docPath'];
        $doTriples = $data['doTriples'];

        //Collections
        $articleCollection  = $this->mongo->articles;
        $journalsCollection = $this->mongo->journals;
        $journalsCollection->ensureIndex('journal');

        //Do it
        try {

            //Get the builder
            $builder = $this->app['builder'];

            //Build document
            $doc = $builder->buildDocument($docPath);

            //Foreach vocabulary in the document, get the number of terms and topics
            //and dump them into the database.
            $vocabStats = $this->extractVocabInfo($doc);
            
            //Do the journal info
            if ($doc->getJournal()) {
                $journalsCollection->update(
                    array('journal' => $doc->getJournal()),
                    array('$inc'    => array('articles' => 1)),
                    array('upsert'  => true)
                );
            }

            //Article collection
            $articleInfo = array('name' => basename($docPath, '.rdf'), 'status' => 'succeed');

            if ($doTriples) {
                $articleInfo['triples'] = $this->app['fileclient']->countTriples($docPath);
            }

            $articleCollection->insert($articleInfo);
            $job->sendComplete($docPath);
        }
        catch (\Exception $e) {

            //Else add it to the database
            $articleCollection->insert(array('name' => basename($docPath, '.rdf'), 'status' => 'fail'));
            $job->sendFail();
        }        
    }

    // --------------------------------------------------------------

    /**
     * Extract items from document
     *
     * @param Bioteawebapi\Entities\Document $doc
     */
    private function extractVocabInfo(Document $doc)
    {
        echo "Processing {$doc}...\n";

        //Docs per vocabulary 
        //Terms per vocabulary
        //Topics per vocabulary

        //Docs per vocabulary
        $vdoccoll = $this->mongo->vocabdoccounts;
        $vdoccoll->ensureIndex('vocabulary');

        foreach($doc->getVocabularies() as $vocab) {
            $vdoccoll->update(
                array('vocabulary' => $vocab->getShortName()),
                array('$inc' => array('numdocs' => 1)),
                array('upsert' => true)
            );
        }

        //Terms and topics per vocabulary
        foreach($doc->getTerms() as $term) {

            $termStr  = (string) $term;

            foreach($term->getTopics () as $topic) {

                $topicStr = (string) $topic;

                if ($topic->getVocabulary()) {
                    $vocabName = $topic->getVocabulary()->getShortName();
                    $collName  = 'vocab_' . $vocabName;
                    $coll = $this->mongo->$collName;
                    $coll->ensureIndex(array('type' => 1, 'value' => 1));

                    $coll->update(
                        array('type'   => 'term', 'value' => $termStr),
                        array('$inc'   => array('count' => 1)),
                        array('upsert' => true)
                    );

                    $coll->update(
                        array('type'   => 'topic', 'value' => $topicStr),
                        array('$inc'   => array('count' => 1)),
                        array('upsert' => true)
                    );
                }
            }
        }
    }
}

/* EOF: DocsStats.php */
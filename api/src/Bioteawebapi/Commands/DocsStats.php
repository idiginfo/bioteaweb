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

use TaskTracker\Tracker;
use TaskTracker\OutputHandler\SymfonyConsole as TrackerConsoleHandler;

/**
 * Get some statistics from the documents
 */
class DocsStats extends Command
{
    /**
     * @var array
     */
    private $stats = array();

    /**
     * @var \MongoDB
     */
    private $db;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('docs:stats')->setDescription('Get some statistics from the documents');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit number of documents analyzed', 0);
        $this->addOption('outfile', 'o', InputOption::VALUE_REQUIRED, 'Optional output file (default is to print to stdout');
        $this->addOption('dumpinterval', 'd', InputOption::VALUE_REQUIRED, 'Dump Interval', 500);
        $this->addOption('mongodb', 'm', InputOption::VALUE_REQUIRED, 'Mongo Database Connection String', 'mongodb://localhost:27017');
    }


    // --------------------------------------------------------------

    /** @inherit */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Let's just log the errors; not report them
        error_reporting(0);

        //Setup tracker
        $tracker = new Tracker(new TrackerConsoleHandler($output));

        //Get the file manager and builder
        $filemgr = $this->app['fileclient'];
        $builder = $this->app['builder'];

        //Output statistics and failed docs statistics
        $failed = array();

        //Count and tracker
        $count = 0;
        $limit = $input->getOption('limit');
        $tracker->start();

        //Setup stats
        $this->stats  = array();
        $journalStats = array();

        //Setup database (don't catch exception)
        $conn = new \Mongo($input->getOption('mongodb'));
        $this->db = $conn->bioteaindexstats;

        //Main loop
        while ($docPath = $filemgr->getNextFile()) {

            //Get out if past limit
            if ($limit && $count >= $limit) {
                break;
            }

            //Dump?
            if ($count % $input->getOption('dumpinterval') == 0) {
                $this->dump($this->stats, $journalStats);
                $this->stats = array();
                $journalStats = array();
            }

            //Do it
            try {

                //Build document
                $doc = $builder->buildDocument($docPath);

                //Foreach vocabulary in the document, get the number of terms and topics
                $this->extractVocabInfo($doc);

                //Do the journal info
                if ($doc->getJournal()) {

                    if (isset($journalStats[$doc->getJournal()])) {
                        $journalStats[$doc->getJournal()]++;
                    }
                    else {
                        $journalStats[$doc->getJournal()] = 1;
                    }

                }

                $tracker->tick('Processing...', \TaskTracker\Tick::SUCCESS);
                $count++;
            }
            catch (\Exception $e) {
                $failed[] = $docPath;
                $tracker->tick('Processing...', \TaskTracker\Tick::FAIL);
            }            
        }

        //One last dump
        $this->dump($this->stats, $journalStats);
        $tracker->finish();

        //Final Report
        ob_start();

        echo "\nVocabularies\n============================================";
        foreach($this->db->listCollections() as $coll) {

            $collName = $coll->getName();

            if (substr($collName, 0, 6) == 'vocab_') {
                echo "\n" . substr($collName, 6);
                echo "\n\tTerms:  " . $this->db->$collName->find(array('type' => 'terms'))->count();
                echo "\n\tTopics: " . $this->db->$collName->find(array('type' => 'topics'))->count();
                echo "\n\tDocs:   " . $this->db->$collName->find(array('type' => 'docs'))->count();
            }
        }

        echo "\n\n";
        echo "\nJournals\n============================================";
        foreach($this->db->journals->find() as $journal) {
            echo "\n\t" . $journal['name'] . ": " . $journal['count'];
        }
        echo "\n\n";

        //Output it
        if ($input->getOption('outfile')) {
            file_put_contents($input->getOption('outfile'), ob_get_clean());
            $output->writeln("Wrote output to: " . $input->getOption('outfile'));
        }
        else {
            $output->write(ob_get_clean());
        }

        $this->db->drop();      
    }

    // --------------------------------------------------------------

    /**
     * Dump to Mongo
     *
     * @var array $vocabs
     * @var array $journals
     */
    private function dump($vocabs, $journals)
    {
        //Dump Vocabs
        foreach ($vocabs as $vocab => $types) {

            $vocabCollName = 'vocab_' . $vocab;
            $vocabColl = $this->db->$vocabCollName;

            foreach ($types as $type => $items) {

                foreach ($items as $item => $count) {

                    $result = $vocabColl->findOne(array('type' => $type, 'item' => $item));

                    if ($result) {
                        $newCount = $count + (int) $result['count'] + $count;
                        $vocabColl->update(
                            array('type' => $type, 'item' => $item),
                            array('type' => $type, 'item' => $item, 'count' => $newCount)
                        );
                    }
                    else {
                        $vocabColl->insert(array('type' => $type, 'item' => $item, 'count' => $count));
                    }
                }
            }
        }

        //Dump Journals
        $journalColl = $this->db->journals;
        foreach ($journals as $journal => $count) {


            $result = $journalColl->findOne(array('name' => $journal));

            if ($result) {
                $newCount = $count + (int) $result['count'];
                $journalColl->update(array('name' => $journal), array('name' => $journal, 'count' => $newCount));
            }
            else {
                $journalColl->insert(array('name' => $journal, 'count' => $count));
            }
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
        $docStr = md5((string) $doc);

        //Local docStats
        $docStats = array();

        //Get items
        foreach($doc->getAnnotations() as $annot) {
            
            $term    = $annot->getTerm();
            $termStr = md5((string) $term);

            foreach($term->getTopics() as $topic) {

                $topicStr = md5((string) $topic);

                $vocab = $topic->getVocabulary();

                //If vocab...
                if ($vocab) {

                    $vocabName = $vocab->getShortName();

                    //Setup vocab array if not already set
                    if ( ! isset($docStats[$vocabName])) {
                        $docStats[$vocabName] = $this->initVocabArray();
                        $docStats[$vocabName]['docs'][$docStr] = 1;
                    }

                    //Increment terms only once
                    if ( ! isset($docStats[$vocabName]['terms'][$termStr])) {
                        $docStats[$vocabName]['terms'][$termStr] = 1;
                    }

                    //Increment topics every time
                    if ( ! isset($docStats[$vocabName]['topics'][$topicStr])) {
                        $docStats[$vocabName]['topics'][$topicStr] = 1;
                    }
                    else {
                        $docStats[$vocabName]['topics'][$topicStr]++;
                    }
                }
            }
        }

        //Merge items with master array
        foreach($docStats as $vocab => $types) {

            if ( ! isset($this->stats[$vocab])) {
                $this->stats[$vocab] = $types;
            }
            else {

                foreach ($types as $type => $items) {

                    foreach($items as $item => $num) {

                        if ( ! isset($this->stats[$vocab][$type][$item])) {
                            $this->stats[$vocab][$type][$item] = $num;
                        }
                        else {
                            $this->stats[$vocab][$type][$item] += $num;
                        }
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------

    /**
     * Initialize a blank vocabulary array
     *
     * @return array
     */
    private function initVocabArray()
    {
        return array(
            'docs'     => array(),
            'terms'    => array(),
            'topics'   => array(),
        );
    }
}

/* EOF: DocsStats.php */
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
    private $stats = array();

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('docs:stats')->setDescription('Get some statistics from the documents');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit number of documents analyzed', 0);
        $this->addOption('outfile', 'o', InputOption::VALUE_REQUIRED, 'Optional output file (default is to print to stdout');
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

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

        //Main loop
        while ($docPath = $filemgr->getNextFile()) {

            //Get out if past limit
            if ($limit && $count > $limit) {
                break;
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

        $tracker->finish();

        //Final Report
        ob_start();

        echo "\nVocabularies\n============================================";
        foreach($this->stats as $vocab => $items) {
            echo "\n" . $vocab;
            echo "\n\tTerms:  " . array_sum($items['terms']);
            echo "\n\tTopics: " . array_sum($items['topics']);
            echo "\n\tDocs:   " . array_sum($items['docs']);
        }

        echo "\n\n";
        echo "\nJournals\n============================================";
        foreach($journalStats as $journal => $count) {
            echo "\n" . $journal . ":   " . $count;
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

                    $vocabUri = $vocab->getUri();

                    //Setup vocab array if not already set
                    if ( ! isset($docStats[$vocabUri])) {
                        $docStats[$vocabUri] = $this->initVocabArray();
                        $docStats[$vocabUri]['docs'][$docStr] = 1;
                    }

                    //Increment terms only once
                    if ( ! isset($docStats[$vocabUri]['terms'][$termStr])) {
                        $docStats[$vocabUri]['terms'][$termStr] = 1;
                    }

                    //Increment topics every time
                    if ( ! isset($docStats[$vocabUri]['topics'][$topicStr])) {
                        $docStats[$vocabUri]['topics'][$topicStr] = 1;
                    }
                    else {
                        $docStats[$vocabUri]['topics'][$topicStr]++;
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
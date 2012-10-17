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
use Bioteawebapi\Entities\Document;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A tool to dump information about docsets to the CLI
 */
class DumpDocument extends Command
{
    protected function configure()
    {
        $this->setName('dump')->setDescription('Build and dump a RDF docset (terms, vocabularies, and topics)');
        $this->addArgument('path',  InputArgument::REQUIRED, 'Path to the single RDF XML file to dump.  Can be relative or absolute');
    }    

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Check path
        $path = $input->getArgument('path');

        if (is_readable($path)) {
            $fullPath = realpath($path);
            $relPath = basename($path);
        }
        else {
            $fullPath = $this->app['fileclient']->resolvePath($path);
            $relPath  = basename($path);

            if ( ! is_readable($fullPath)) {
                throw new \InvalidArgumentException("Invalid Path specified. Cannot read from "
                                                    . $this->getArgument('path'));

            }
        }

        $document = $this->app['builder']->buildDocument($fullPath, $relPath);

        //Generate Report
        $output->writeln($this->generateReport($document));
    }

    // --------------------------------------------------------------

    /**
     * Generate a simple report
     *
     * @param BioteaDocSet $docset
     * @return string  Fit for output to the console
     */
    protected function generateReport(Document $document)
    {
        $output  = "\n";
        $output .= "Filepath: " . $document->getRDFFilePath() . "\n";

        foreach($document->getAnnotationFilePaths() as $name => $path) {
            $output .= sprintf("Annotation File %s: %s\n", $name, $path);
        }

        $output .= "\n-- Terms -----------------------------------\n";

        foreach($document->getTerms() as $term) {
            $output .= "\nTerm: " . (string) $term . " (topics below)";
            $output .= "\n\t" . implode("\n\t", array_map(function($t) { return $t->getTopicUri(); }, $term->getTopics()));
        }

        $output .= "\n\n";

        $output .= "\n-- Topics ----------------------------------\n";
        foreach($document->getTopics() as $topic) {
            $output .= sprintf("\nTopic: %s, URI: %s", ($topic->getShortName() ?: '[no short name]'), $topic->getUri());
        }

        $output .= "\n\n";

        $output .= "\n-- Vocabularies ----------------------------\n";
        foreach($document->getVocabularies() as $vocab) {

            $output .= sprintf("\nVocabulary: %s, URI: %s" , $vocab->getShortName(), $vocab->getUri());

        }

        return $output;
    }
}

/* EOF: DumpDocSets.php */
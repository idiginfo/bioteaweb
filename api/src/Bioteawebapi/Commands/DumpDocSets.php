<?php

namespace Bioteawebapi\Commands;
use Bioteawebapi\Models\BioteaDocSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A tool to dump information about docsets to the CLI
 */
class DumpDocSets extends Command
{
    protected function configure()
    {
        $this->setName('dump')->setDescription('Build and dump a RDF docset (terms, vocabularies, and topics)');
        $this->addArgument('path',  InputArgument::REQUIRED, 'Path to the single RDF XML file to dump');
    }    

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Check path
        $path = $input->getArgument('path');
        if (is_dir($path)) {
            throw new \RuntimeException("The path must be a file (not a directory).");
        }
        elseif ( ! is_readable($path)) {
            throw new \RuntimeException(sprintf("The path is not readable: %s", $path));
        }

        //Fix paths
        $fullPath = realpath($path);
        $relPath  = basename($fullPath);

        $docset = $this->app['builder']->buildDocSet($fullPath, $relPath);

        //Generate Report
        $output->writeln($this->generateReport($docset));
    }

    // --------------------------------------------------------------

    /**
     * Generate a simple report
     *
     * @param BioteaDocSet $docset
     * @return string  Fit for output to the console
     */
    protected function generateReport(BioteaDocSet $docset)
    {
        $output  = "\n";
        $output .= "Filepath: " . $docset->getMainFilePath() . "\n";

        foreach($docset->getAnnotationFilePaths() as $name => $path) {
            $output .= sprintf("Annotation File %s: %s\n", $name, $path);
        }

        $output .= "\n-- Terms -----------------------------------\n";

        foreach($docset->getTerms() as $term) {
            $output .= "\nTerm: " . (string) $term . " (topics below)";
            $output .= "\n\t" . implode("\n\t", array_map(function($t) { return $t->getTopicUri(); }, $term->getTopics()));
        }

        $output .= "\n\n";

        $output .= "\n-- Topics ----------------------------------\n";
        foreach($docset->getTopics() as $topic) {
            $output .= sprintf("\nTopic: %s, URI: %s", ($topic->getTopicShortName() ?: '[no short name]'), $topic->getTopicUri());
        }

        $output .= "\n\n";

        $output .= "\n-- Vocabularies ----------------------------\n";
        foreach($docset->getVocabularies() as $name => $uri) {

            $output .= sprintf("\nVocabulary: %s, URI: %s" , $name, $uri);

        }

        return $output;
    }
}

/* EOF: DumpDocSets.php */
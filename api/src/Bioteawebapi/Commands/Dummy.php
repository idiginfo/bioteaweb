<?php

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bioteawebapi\Models\SolrIndexDocument;
use Bioteawebapi\Services\SolrClient;

/**
 * Dummy Command class loads dummy data into SOLR for testing
 *
 * This should only be used for development purposes
 */
class Dummy extends Command
{
    /**
     * @var string  Path to fixtures
     */
    private $fixturesPath;

    // --------------------------------------------------------------
    
    public function __construct($app)
    {
        parent::__construct($app);

        $this->numInserted = 0;
        $this->fixturesPath = __DIR__ . '/../../../tests/fixtures';

        if ( ! is_readable($this->fixturesPath)) {
            throw new \RuntimeException("Cannot read fixtures path.  Does tests/ directory exist?");
        }

    }

    // --------------------------------------------------------------
    
    protected function configure()
    {
        $this->setName('dummy')->setDescription('Load a small set of Dummy data into Solr for testing.');
        $this->addOption('clear', null, InputOption::VALUE_NONE, 'If set, SOLR will be cleared first (warning: destructive!)');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('clear')) {
            $dialog = $this->getHelperSet()->get('dialog');
            $resp = $dialog->askConfirmation($output, '<question>Warning.  This will clear the SOLR index!!  Type \'yes\' to continue</question>', false);

            if ( ! $resp) {
                $output->writeln('Cancelled.');
                return;
            }

            $output->writeln("Clearing SOLR index...");
            $this->app['solr_client']->clearIndex();
        }

        //Used for creating dummy records
        $now = time();
       
        //Load the dummy and test files into memory
        $topics  = file($this->fixturesPath . '/solrDummyData/topics.txt', FILE_IGNORE_NEW_LINES);
        $terms   = file($this->fixturesPath . '/solrDummyData/terms.txt', FILE_IGNORE_NEW_LINES);
        $vocabs  = file($this->fixturesPath . '/solrDummyData/vocabularies.txt', FILE_IGNORE_NEW_LINES);

        //Files
        $mainFiles = scandir($this->fixturesPath . '/rdfSampleFolder');
        $aoFiles   = scandir($this->fixturesPath . '/rdfSampleFolder/AO_annotations');

        //Foreach mainFiles as mainFile, build a dummy record for insertion
        foreach($mainFiles as $fileName) {

            if (preg_match("/^PMC[\d]+?\.rdf$/", $fileName)) {

                //Build
                $doc = new SolrIndexDocument();
                $doc->setRdfFilePath(sprintf('/some/example/%s/%s', $now, $fileName));
                $doc->setTerms($this->array_random($terms, rand(2, count($terms)-1) ));
                $doc->setTopics($this->array_random($topics, rand(2, count($topics)-1) ));
                $doc->setVocabularies($this->array_random($vocabs, rand(2, count($vocabs)-1) ));

                //Update
                $this->app['solr_client']->update($doc);
            }
        }

        $numInserted = $this->app['solr_client']->commit();
        $output->writeln(sprintf("Processed %d documents into SOLR.", $numInserted));
    }    


    private function array_random($arr, $num = 1) {
        shuffle($arr);
        
        $r = array();

        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }

        return $num == 1 ? $r[0] : $r;
    }   
}

/* EOF: Dummy */
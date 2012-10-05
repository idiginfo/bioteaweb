<?php

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Schema\Comparator;
use \Doctrine\DBAL\Schema\Schema;

/**
 * Build MySQL Schema for MySQL indicies
 *
 * A very simple class to ensure that MySQL schema is correct
 */
class BuildSchema extends Command
{
    /**
     * @var 
     */
    private $sm;

    // --------------------------------------------------------------

    public function __construct($app)
    {
        parent::__construct($app);
        $this->sm = $app['db']->getSchemaManager();
    }

    // --------------------------------------------------------------
    
    protected function configure()
    {
        $this->setName('buildschema')->setDescription('Build MySQL Schema for MySQL indicies.  Only changes out-of-date schemas.');
        $this->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'This option allows you to see the queries that would be run.');

    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Analyzing...");

        //Get the current schema
        $sm = $this->app['db']->getSchemaManager();

        $fromSchema = $sm->createSchema();
        $toSchema   = $this->getToSchema(new Schema());

        //Compare to desired schema
        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $queries    = $schemaDiff->toSql($this->app['db']->getDatabasePlatform());

        //If we have queries, run them; otherwise just print a message
        if (count($queries) > 0 && $input->getOption('dry-run')) {

            $output->writeln("The following queries would be run...\n");
            foreach($queries as $ct => $query) {
                $output->writeln("-Query {$ct}---------------------------\n");
                $output->writeln($query);
                $output->writeln("\n");
            }
        }
        elseif (count($queries) > 0 && ! $input->getOption('dry-run')) {

            $output->writeln("Updating...");

            $conn =& $this->app['db'];
            $conn->transactional(function($conn) use ($queries) {
                foreach($queries as $query) {
                    $conn->query($query);
                }
            });

            $output->writeln(sprintf("Updated schema (used %d queries)", count($queries)));
        }
        else {
            $output->writeln("Schema already up-to-date.");
        }
    }

    // --------------------------------------------------------------

    protected function getToSchema($schema)
    {
        //Documents Table
        $docTable = $schema->createTable('documents');
        $docTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $docTable->addColumn('rdfPath', 'string');
        $docTable->addColumn('rdfAnnotationPaths', 'string');
        $docTable->setPrimarykey(array('id'));

        //Terms Table
        $termsTable = $schema->createTable('terms');
        $termsTable->addColumn('term', 'string', array('length' => 255));
        $termsTable->setPrimaryKey(array('term'));

        //Vocabularies Table
        $vocabTable = $schema->createTable('vocabularies');
        $vocabTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));        
        $vocabTable->addColumn('uri', 'string', array('length' => 255));
        $vocabTable->addColumn('shortName', 'string', array('length' => 10));
        $vocabTable->addUniqueIndex(array('uri'));
        $vocabTable->addUniqueIndex(array('shortName'));
        $vocabTable->setPrimarykey(array('id'));

        //Topics Table
        $topicsTable = $schema->createTable('topics');
        $topicsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $topicsTable->addColumn('uri', 'string', array('length' => 255));
        $topicsTable->addColumn('shortName', 'string', array('length' => 10));
        $topicsTable->addColumn('vocabulary_id', 'integer', array('unsigned' => true));
        $topicsTable->addUniqueIndex(array('uri'));
        $topicsTable->addUniqueIndex(array('shortName'));
        $topicsTable->setPrimarykey(array('id'));
        
        //Annotations Table
        $annotationsTable = $schema->createTable('annotations');
        $annotationsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $annotationsTable->addColumn('document_id', 'integer', array('unsigned' => true));
        $annotationsTable->addColumn('term', 'string', array('length' => 255));
        $annotationsTable->setPrimarykey(array('id'));

        //Topics_for_terms Table
        $topicsForTermsTable = $schema->createTable('topics_for_terms');
        $topicsForTermsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $topicsForTermsTable->addColumn('topic_id', 'integer', array('unsigned' => true));       
        $topicsForTermsTable->addColumn('term', 'string', array('length' => 255));               
        $topicsForTermsTable->setPrimarykey(array('id'));

        //Return the schema
        return $schema;
    }
}

/* EOF: BuildSchema.php */
<?php

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build MySQL Schema for MySQL indicies
 *
 * A very simple class to ensure that MySQL schema is correct
 */
class BuildSchema extends Command
{

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

        //If not up-to-date, there will be more than 0 queries to run
        $queries = $this->app['mysql_client']->checkSchema(true);

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
}

/* EOF: BuildSchema.php */
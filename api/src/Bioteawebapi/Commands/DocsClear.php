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
use Doctrine\DBAL\Schema\Table;

use TaskTracker\Tracker;
use TaskTracker\OutputHandler\SymfonyConsole as TrackerConsoleHandler;
use TaskTracker\OutputHandler\Monolog as TrackerMonologHandler;

/**
 * Clear all documents in the system
 */
class DocsClear extends Command
{
    const NO_LIMIT = 0;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('docs:clear')->setDescription('Clear all documents in the system');
        $this->addOption('batchsize', 'b', InputOption::VALUE_REQUIRED, 'Batch Size (default 10,000)', 10000);
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //If not non-interactive, prompt "are you sure?"
        if ( ! $input->getOption('no-interaction')) {

            $dialog = $this->getHelperSet()->get('dialog');
            if ( ! $dialog->askConfirmation($output,
                    '<error>WARNING: This will DESTROY all data.</error> Are you sure you want to do this? [y/n]: ', false
                )) {
                return;
            }
        }

        //Get schema manager
        $sm = $this->app['db']->getSchemaManager();

        //Get batch size
        $batchSize = $input->getOption('batchsize');

        //Foreach table, build delete statement
        $stmnts = array();
        foreach($sm->listTables() as $table) {

            $this->deleteFromTable($table, $batchSize, $output);

        }

        //Do it
        foreach($stmnts as $tname => $stmt) {
            $output->writeln("Deleting contents of " . $tname . "...");
            $stmt->execute();
        }
    }

    // --------------------------------------------------------------

    /**
     * Delete from a table
     *
     * @param Doctrine\DBAL\Schema\Table $table
     * @param int $batchSize
     * @param OutputInterface $output
     */
    protected function deleteFromTable(Table $table, $batchSize, OutputInterface $output)
    {

        //Get table name 
        $tname = $table->getName();

        //Get DBAL\Connection object
        $db = $this->app['db'];

        //Holds total number of deleted records for this table
        $totalCount = 0;

        //Get the key name
        $keyName = $db->executeQuery("SHOW KEYS FROM {$tname} WHERE Key_name = 'PRIMARY';")->fetchColumn(4);

        $sq = sprintf('SELECT count(%s) AS count FROM %s', $keyName, $tname);
        $dq = sprintf('DELETE FROM %s LIMIT %s', $tname, $batchSize);

        //Total number of rows to be deleted
        $totalNumRows = $db->executeQuery($sq)->fetchColumn(0);

        //Setup a tasktracker
        $tracker = new Tracker(new TrackerConsoleHandler($output), $totalNumRows);
        $tracker->start("Starting {$tname}");

        //Delete in chunks
        while($db->executeQuery($sq)->fetchColumn(0) > 0) {

            //Do it
            $rows = $db->executeQuery($dq)->rowCount();

            //Reporting
            $totalCount += $rows;
            $tracker->tick(sprintf(
                "Deleting %s rows from %s in batches of %s [%s]",
                number_format($totalNumRows, 0),
                $tname,
                number_format($batchSize, 0),
                number_format($totalCount, 0)
            ));
        }

        $tracker->finish("Done with table {$tname}");

        return $totalCount;
    }
}

/* EOF: DocsClear.php */
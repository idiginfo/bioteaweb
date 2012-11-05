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

/**
 * Clear all documents in the system
 */
class DocsClear extends Command
{
    const NO_LIMIT = 0;

    protected function configure()
    {
        $this->setName('docs:clear')->setDescription('Clear all documents in the system');
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

        //Get schema manager and database clients
        $db = $this->app['db'];
        $sm = $this->app['db']->getSchemaManager();

        //Foreach table, build delete statement
        $stmnts = array();
        foreach($sm->listTables() as $table) {
            $tname     = $table->getName();
            $stmnts[$tname] = $db->prepare("DELETE FROM {$tname};");
        }

        //Do it
        foreach($stmnts as $tname => $stmt) {
            $output->writeln("Deleting contents of " . $tname . "...");
            $stmt->execute();
        }
    }
}

/* EOF: DocsClear.php */
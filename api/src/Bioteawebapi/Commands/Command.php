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
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Silex\Application;

/**
 * Bioteaweb Command extends SymfonyCommmand and injects Silex App
 */
abstract class Command extends SymfonyCommand
{
    protected $app;

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(Application $app)
    {
        //Run parent constructor
        parent::__construct();

        //Set dependencies
        $this->app = $app;
    }

    // --------------------------------------------------------------   

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("This command has not been programmed yet.");
    }

}

/* EOF: Command.php */
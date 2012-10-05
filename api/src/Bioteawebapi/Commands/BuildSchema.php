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
    public function __construct($app)
    {
        parent::__construct($app);

    }

    // --------------------------------------------------------------
    
    protected function configure()
    {
        $this->setName('buildschema')->setDescription('Build MySQL Schema for MySQL indicies.  Only changes out-of-date schemas.');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("HELLO");
    }   
}

/* EOF: BuildSchema.php */
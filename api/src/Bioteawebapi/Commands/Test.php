<?php

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Abstracts\Command
{
    // --------------------------------------------------------------
	
    protected function configure()
    {
        $this->setName('test')->setDescription('Test Command. Ignore');
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$output->writeln("Hello");
    }
}

/* EOF: Test.php */
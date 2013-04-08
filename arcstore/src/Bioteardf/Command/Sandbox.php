<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class Sandbox extends Command
{
    private $app;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('sandbox');
        $this->setDescription('A Sandbox');
        $this->setHelp("For messing around");
    }

    // --------------------------------------------------------------

    protected function init(Application $app)
    {
        //Get reference to whole app
        $this->app = $app;
    }

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = realpath('/vagrant/web/bioteaweb/api/tests/fixtures/rdfSampleFolder/');

        foreach($this->app['files']->getIterator($path) as $f) {
            var_dump($f->getBasename());
        }

    }    
}

/* EOF: Sandbox.php */
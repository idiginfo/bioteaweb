<?php

namespace Bioteardf\Command;

use Silex\Application;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;

/**
 * Abstract command class
 */
abstract class Command extends SymfonyConsoleCommand
{
    /**
     * @var Silex\Application
     */
    private $app;

    // --------------------------------------------------------------

    public function connect(Application $app)
    {
        $this->app = $app;
        $this->init($app);
    }

    // Descendants need to implement (from SymfonyConsoleCommand):
    //
    //   protected function configure()    
    //   protected function execute(InputInterface $input, OutputInterface $output)
    
    // --------------------------------------------------------------
    
    protected function init(Application $app)
    {
        //nothing.  this is meant to be overridden if necessary
    }

    // --------------------------------------------------------------
    
    /**
     * Log a message to Monolog
     * 
     * @param string $level
     * @param string $message
     * @param array  $context
     * @return boolean  Whether the record has been processed
     */
    protected function log($level, $message, array $context = array())
    {
        $method = 'add' . ucfirst(strtolower($level));
        if (method_exists($this->app['monolog'], $method)) {
            return call_user_func(array($this->app['monolog'], $method), $message, $context);
        }
        else {
            throw new \InvalidArgumentException(sprintf(
                "The logging level '%s' is invalid. See Monolog documentation for valid log levels",
                $level
            ));
        }
    }
}

/* EOF: Command.php */
<?php

namespace Bioteardf;

use Minions\Command\Workers as MinionsWorkerCommand;
use Minions\Driver\Redis as MinionsRedisDriver;
use Minions\Client as MinionsClient;
use Pimple;

use Symfony\Component\Console\Application as ConsoleApp;
use Bioteardf\Command\Command as BioteaCommand;
use Silex\Application as SilexApp;
use Configula\Config;
use Exception;

class App extends SilexApp
{
    /**
     * @var string
     */
    private $basepath;

    // --------------------------------------------------------------

    /**
     * Static MAIN Method
     */
    public static function main()
    {
        $className = get_called_class();
        $cls = new $className();

        if (php_sapi_name() == 'cli') {
            return $cls->execCli();
        }
        else {
            return $cls->execWeb();
        }
    }

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->basepath = realpath(__DIR__ . '/../../');
        $this->loadLibraries();
    }

    // --------------------------------------------------------------

    /**
     * Run the web application
     */
    public function execWeb()
    {
        $app =& $this;

        $this->get('/', function() use ($app) {
            ob_start();
            $app['arc2.sparql']->go();
            return ob_get_clean();
        });

        parent::run();
    }

    // --------------------------------------------------------------

    /**
     * Execute CLI App
     */
    public function execCli()
    {
        //Console app
        $consoleApp = new ConsoleApp('Biotea');

        //Command Register
        $app =& $this;
        $register = function(BioteaCommand $cmd) use ($consoleApp, $app) {
            $cmd->connect($app);
            $consoleApp->add($cmd);
        };

        //Add Commands
        $register(new Command\RdfLoad());
        $register(new Command\Sandbox());
        $register(new Command\UtilDb());

        //Run it
        return $consoleApp->run();                
    }

    // --------------------------------------------------------------

    /** 
     * Get the basepath with optional subpath
     */
    protected function basepath($subpath = '')
    {
        if ($subpath) {
            $subpath = '/' . trim($subpath, DIRECTORY_SEPARATOR);
        }

        return realpath($this->basepath . $subpath);
    }

    // --------------------------------------------------------------

    /**
     * Load Libraries
     */
    private function loadLibraries()
    {
        $app =& $this;

        //$app['config']
        $app['config'] = new Config($this->basepath('config'));

        //$app['arc2.store']
        //$app['arc2.sparql']
        //$app['arc2.parser']
        $app->register(new Provider\Arc2ServiceProvider(), array(
            'arc2.config'     => (array) $app['config']->rdfstore,
            'arc2.store_name' => 'biotea'
        ));

        //$app['files']
        $app['files'] = $app->share(function() use ($app) {
            return new Service\RDFFileService;
        });

        //$app['loader']
        $app['loader'] = $app->share(function() use ($app) {
            return new Service\RdfLoader($app['arc2.store']);
        });

        //$app['minons.tasks']
        $app['minions.tasks'] = new Pimple();
        $app['minions.tasks']['load_file'] = $app['minions.tasks']->share(function() use ($app) {
            return new Task\LoadRdfFile($app['loader']);
        });

        //$app['minions.client']
        //$app['minions.cmd.workers']
        $app->register(new Provider\MinionsServiceProvider(), array(
            'minions.driver' => new MinionsRedisDriver($app['config']->redis['host'], $app['config']->redis['port']),
            'minions.tasks'  => $app['minions.tasks']
        ));

        //$app['db']
        //LEFT OFF HERE -- Use the Doctrine Loader to load Doctrine up for file state management
        //ALSO - Fix URI of graph so that it uses the Biotea URL (maybe a config parameter -- dunno)
        //Once we can track the state of processed files, then we want to make overwriting optional on RDFLoader class
    }
}

/* EOF: App.php */

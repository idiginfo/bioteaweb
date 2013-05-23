<?php

namespace Bioteardf;

use Minions\Command\Workers as MinionsWorkerCommand;
use Minions\Driver\Redis as MinionsRedisDriver;
use Minions\Client as MinionsClient;
use Pimple;

use Symfony\Component\Console\Application as ConsoleApp;
use Bioteardf\Command\Command as BioteaCommand;
use Silex\Application as SilexApp;
use Silex\Provider as SilexProvider;
use Nutwerk\Provider\DoctrineORMServiceProvider;
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

        //Add Biotea Commands
        $register(new Command\RdfLoad());
        $register(new Command\RdfIndex());
        $register(new Command\RdfLoadStatus());
        $register(new Command\Sandbox());
        $register(new Command\IndexReports());
        $register(new Command\UtilDb($app['dispatcher']));

        //Add other commands
        $consoleApp->add(new MinionsWorkerCommand($app['minions.driver'], $app['minions.tasks'], $app['dispatcher']));

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

        //$app['db']
        $app->register(new SilexProvider\DoctrineServiceProvider(), array(
            'db.options' => array (
                'driver'    => $app['config']->indexesdb['driver'],
                'host'      => $app['config']->indexesdb['host'],
                'dbname'    => $app['config']->indexesdb['dbname'],
                'user'      => $app['config']->indexesdb['user'],
                'password'  => $app['config']->indexesdb['password'],
            )
        ));

        //$app['db.orm.em']
        $app->register(new DoctrineORMServiceProvider(), array(
            'db.orm.proxies_dir' => (isset($app['config']->mysql['cachedir']))
                ? $app['config']->mysql['cachedir']
                : sys_get_temp_dir(),
            'db.orm.proxies_namespace'     => 'DoctrineProxy',
            'db.orm.auto_generate_proxies' => true,
            'db.orm.entities'              => array(array(
                'type'      => 'annotation',
                'path'      => $this->basepath('src/Bioteardf/Model'),
                'namespace' => 'Bioteardf\Model'
            ))
        ));

        //$app['minons.tasks']
        $app['minions.tasks'] = new Pimple();
        $app['minions.tasks']['load_set'] = $app['minions.tasks']->share(function() use ($app) {
            return new Task\LoadRdfSet($app['loader']);
        });
        $app['minions.tasks']['index_set'] = $app['minions.tasks']->share(function() use ($app) {
            return new Task\IndexRdfSet($app['tracker'], $app['parser'], $app['persister']);
        });

        //$app['minions.client']
        //$app['minions.cmd.workers']
        $app->register(new Provider\MinionsServiceProvider(), array(
            'minions.driver' => new MinionsRedisDriver($app['config']->redis['host'], $app['config']->redis['port']),
            'minions.tasks'  => $app['minions.tasks']
        ));

        //$app['dbmgr']
        $app['dbmgr'] = $app->share(function() use ($app) {
            $mgr = new Service\DatabaseManager(
                $app['arc2.store'],
                new Service\DoctrineEntityManager($app['db.orm.em']),
                $app['minions.client']
            );
            $mgr->setDispatcher($app['dispatcher']);
            return $mgr;
        });

        //$app['files']
        $app['files'] = $app->share(function() use ($app) {
            return new Service\RdfFileService;
        });

        //$app['parser']
        $app['parser'] = $app->share(function() use ($app) {
            return new Service\Indexes\BioteaRdfSetParser(
                new Service\Indexes\DocObjectRegistryFactory($app['db.orm.em']),
                new Service\Indexes\MainDocParser(),
                new Service\Indexes\AnnotationSetParser($app['config']->vocabularies ?: array())
            );
        });

        //$app['tracker']
        $app['tracker'] = $app->share(function() use ($app) {
            return new Service\RdfSetTracker($app['db.orm.em']);
        });

        //$app['persister']
        $app['persister'] = $app->share(function() use ($app) {
            return new Service\Indexes\DocObjectPersister($app['db.orm.em']);
        });

        //$app['loader']
        $app['loader'] = $app->share(function() use ($app) {
            return new Service\TripleStore\RdfLoader($app['arc2.store'], $app['tracker']);
        });

        //$app['indexes.reports']
        $app['indexes.reports'] = $app->share(function() use ($app) {

            $reportBag = new Pimple(array(
                'numDocsPerJournalWithMM' => new Service\Indexes\Report\NumDocsPerJournalWithMM(),
                'TermsNumInstancesInMM'   => new Service\Indexes\Report\TermsNumInstancesInMM()
            ));

            return new Service\Indexes\ReportRunner($reportBag, $app['db.orm.em']);
        });
    }
}

/* EOF: App.php */
